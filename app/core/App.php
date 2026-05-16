<?php

class App
{
	public function init()
	{
		if (!$_GET['url']) {
			require '../app/controllers/index.php';
			return;
		}

		$URL = explode('/', $_GET['url']);
		$URL_NEXT = array_slice($URL, 1);

		$controllerName = ucfirst($URL[0]);
		$filename = "../app/controllers/" . $controllerName . "/index.php";
		if (file_exists($filename)) {
			require $filename;
			$controller = new $controllerName($controllerName . "/", $URL_NEXT);
		} else {

			$basePath = "../app/controllers";
			$entries = scandir($basePath);

			$found = false;

			foreach ($entries as $entry) {
				$fullPath = $basePath . '/' . $entry;

				// Skip non-directories
				if (!is_dir($fullPath)) continue;

				// Check if folder name matches pattern like [something]
				if (preg_match('/^\[.+\]$/', $entry)) {
					$found = true;
					break;
				}
			}
			if($found)
			{
				$filename = "../app/controllers/" . $entry . "/index.php";
				require $filename;
				preg_match('/\[(.*?)\]/', $entry, $controllerName);
				$controllerName = $controllerName[1];
				
				$SLUG_DATA[$controllerName] = $URL[0];

				new $controllerName("[".$controllerName."]"."/",$URL_NEXT,$SLUG_DATA);
			}

			if (!$found) {
				echo "No folder matching [some-code] pattern found.";
			}
		}
	}
}
