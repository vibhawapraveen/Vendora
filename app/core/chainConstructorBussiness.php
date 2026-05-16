<?php
		if(!$URL || !$URL[0])
		{
			$this->index();
			return;
		}

		$URL_NEXT = array_slice($URL, 1);
		$controllerName = ucfirst($URL[0]);

		echo $PREV_URL;

		$filename = "../app/controllers/Business/".$PREV_URL.$controllerName."/index.php";
		if(file_exists($filename))
		{
			require $filename;
			$controller = new $controllerName($PREV_URL."/".$controllerName."/",$URL_NEXT);

		}else
		{
			echo "NOT FOUND";
		}
?>