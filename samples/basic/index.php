<?php

include(__DIR__."/../../ewiki.php");


$e_wiki = new EWiki(__DIR__."/ewiki", $_SERVER['REQUEST_URI']);

?>
<html>
<head>
</head>
<body>
	<?php echo $e_wiki->getHTML(); ?>
</body>
</html>
