<?php
		if(!$URL || !$URL[0])
		{
			$this->index();
			return;
		}

		$URL_NEXT = array_slice($URL, 1);
		$controllerName = ucfirst($URL[0]);
		$filename = "../app/controllers/".$PREV_URL.$controllerName."/index.php";
		if(file_exists($filename))
		{
			require $filename;
			$controller = new $controllerName($PREV_URL."/".$controllerName."/",$URL_NEXT,$SLUG_DATA);

		}else
		{
			$basePath = "../app/controllers/".$PREV_URL;
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
				$filename = "../app/controllers/" .$PREV_URL."/". $entry . "/index.php";
				require $filename;
				preg_match('/\[(.*?)\]/', $entry, $controllerName);
				$controllerName = $controllerName[1];
				
				$SLUG_DATA[$controllerName] = $URL[0];

				new $controllerName($PREV_URL."/[".$controllerName."]"."/",$URL_NEXT,$SLUG_DATA);
			}

			if (!$found) {
				echo "No folder matching [some-code] pattern found.";
			}
		}
?>