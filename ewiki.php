<?php

require(__DIR__."/3rdparty/filemimetype/filemimetype.php");
require(__DIR__."/3rdparty/parsedown/Parsedown.php");

class EWiki
{
	
	private $pagesDirPath = null;
	
	private $html = "";
	
	public function __construct($relative_pages_dir_path, $uri)
	{
		$this->initialize($relative_pages_dir_path);		
		
		$file_info = $this->getPageFileInfo($uri);
		
		if ($file_info->getType() == FileInfo::TYPE_NOT_FOUND)
			$this->parse("###EWiki page does not exist.###");
		else if ($file_info->getType() == FileInfo::TYPE_MARKDOWN)
			$this->parse(file_get_contents($file_info->getFilePath()));
		else if ($file_info == FileInfo::TYPE_FILE)
			$this->outputFile($file_info->getFilePath());
	}
	
	public function getHTML()
	{
		return $this->html;
	}
	
	private function initialize($relative_pages_dir_path)
	{
		$pages_dir_path = realpath($relative_pages_dir_path);
		if ($pages_dir_path !== false)
			$this->pagesDirPath = $pages_dir_path;
		
		if (!file_exists($this->pagesDirPath . '/statistics'))
			mkdir($this->pagesDirPath . '/statistics');
	}
	
	private function getPageFileInfo($uri)
	{
		return new FileInfo($this->pagesDirPath, $uri);
	}
	
	private function parse($text)
	{
		$text = $this->parse_Markdown($text);
		$text = $this->parse_Links($text);
		
		$this->html = $text;
	}
	
	private function parse_Markdown($markdown)
	{
		$parsedown = new Parsedown();
	
		return $parsedown->text($markdown);
	}
	
	private function parse_Links($markdown)
	{
		return $markdown;
	}
	
	private function outputFile($file_path)
	{
		/*
			$file = fopen($file_path, "w+");
			if (flock($fp, LOCK_EX)) {
				fwrite($fp, $data);
				flock($fp, LOCK_UN);
			}
			fclose($fp);
		 */
		
		$speed = 1024;
		
		if (is_file($file_path) === true)
		{
			set_time_limit(0);
	
			while (ob_get_level() > 0)
				ob_end_clean();
	
			$file_size = sprintf('%u', filesize($file_path));
			$speed = (is_int($speed) === true) ? $size : intval($speed) * 1024;
	
			header('Expires: 0');
			header('Pragma: public');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Content-Type: application/octet-stream');
			header('Content-Length: ' . $file_size);
			header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
			header('Content-Transfer-Encoding: binary');
	
			for ($i = 0; $i <= $size; $i = $i + $speed) {
				file_get_contents($path, false, null, $i, $speed);
				ob_end_flush();
				sleep(1);
			}
	
			exit();
		}
	
		return false;
	}
}

class FileInfo
{

	const TYPE_NOT_FOUND = 0;
	const TYPE_MARKDOWN = 1;
	const TYPE_FILE = 2;
	
	
	private $filePath = null;
	private $type = FileInfo::TYPE_NOT_FOUND;
	
	public function __construct($dir_path, $uri)
	{
		if ($dir_path === null)
			return;
		
		if ($uri[mb_strlen($uri) - 1] === '/')
			$uri = mb_substr($uri, 0, mb_strlen($uri) - 1);
		
		$file_path = realpath($dir_path . '/' . $uri);
		if (strpos($file_path, $dir_path) !== 0)
			return;
		
		$file_path = $dir_path . '/' . $uri . ".md";
		if (file_exists($file_path)) {
			$this->filePath = $file_path;
			$this->type = FileInfo::TYPE_MARKDOWN;
			return;
		}
		
		$file_path = $dir_path . '/' . $uri . "/home.md";
		if (file_exists($file_path)) {
			$this->filePath = $file_path;
			$this->type = FileInfo::TYPE_MARKDOWN;
			return;
		}
		
		$file_path = $dir_path . '/' . $relative_file_path;
		if (file_exists($file_path)) {
			$this->filePath = $file_path;
			$this->type = FileInfo::TYPE_FILE;
			return;
		}
	}
	
	public function getType()
	{
		return $this->type;
	}
	
	public function getFilePath()
	{
		return $this->filePath;
	}
	
}