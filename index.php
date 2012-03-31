<?php
error_reporting(E_ALL & ~E_NOTICE);
set_time_limit(0);

function ifnull($v, $def) {
	return $v ? $v : $def;
}

function read_all_files($root = '.'){
  $files  = array('files'=>array(), 'dirs'=>array());
  $directories  = array();
  $last_letter  = $root[strlen($root)-1];
  $root  = ($last_letter == '\\' || $last_letter == '/') ? $root : $root.DIRECTORY_SEPARATOR;
  $directories[]  = $root; 
  while (sizeof($directories)) {
    $dir  = array_pop($directories);
    if ($handle = opendir($dir)) {
      while (false !== ($file = readdir($handle))) {
        if ($file == '.' || $file == '..') {
          continue;
        }
		
        if (is_dir($dir.$file)) {
          $directory_path = $dir.$file.DIRECTORY_SEPARATOR;
          array_push($directories, $directory_path);
          $files['dirs'][]  = $directory_path;
        } elseif (is_file($dir.$file)) {
          $files['files'][]  = $dir.$file;
        }
      }
      closedir($handle);
    }
  } 
  return $files;
}

function write_package($p, $selected = false) {
	echo "<package>";
	echo "<id>deadbeefa00000000000000$i</id>";
	echo "<package>deadbeefb00000000000000$i</package>";
	echo "<display_name>" . $p["name"] . "-" . $p["version"] . "</display_name>";
	echo "<name>" . $p["name"] . "</name>";
	echo "<version>" . $p["version"] . "</version>";
	echo "<version_major>" . $p["version_major"] . "</version_major>";
	echo "<version_minor>" . $p["version_minor"] . "</version_minor>";
	echo "<version_patch>" . $p["version_patch"] . "</version_patch>";
	echo "<release>" . $p["version_release"] . "</release>";
	echo "<vrelease>" . $p["version_release"] . "</vrelease>";
	echo "<vrelease_index/>";
	echo "<author>" . ifnull($p["author"], "unknown") . "</author>";
	echo "<description>" . htmlspecialchars(ifnull($p["description"], "No description for this package")) . "</description>";
	echo "<instructions>" . htmlspecialchars(ifnull($p["instruction"], "No instructions for this package")) . "</instructions>";
	echo "<changelog>" . $p["changelog"] . "</changelog>";
	echo "<createdon>" . strftime("%Y-%m-%dT%H:%M:%SZ", time ()) . "</createdon>";
	echo "<createdby>" . ifnull($p["author"], "unknown") . "</createdby>";
	echo "<editedon>" . strftime("%Y-%m-%dT%H:%M:%SZ", time ()) . "</editedon>";
	echo "<releasedon>" . strftime("%Y-%m-%dT%H:%M:%SZ", time ()) . "</releasedon>";
	echo "<downloads>0</downloads>";
	echo "<approved>" . ifnull($p["approved"], "true") . "</approved>";
	echo "<audited>" . ifnull($p["audited"], "true") . "</audited>";
	echo "<featured>" . ifnull($p["featured"], "true") . "</featured>";
	echo "<deprecated>" . ifnull($p["deprecated"], "false") . "</deprecated>";
	echo "<license>" . ifnull($p["license"], "GPLv2") . "</license>";
	echo "<smf_url/>";
	for ($j = 0; $j < count($repos); $j++) {
		$r = $repos[$j];
		if ($r["name"] == $p["repo"]) {
			echo "<repository>deadbeefc00000000000000$j</repository>";
		}
	}
	echo "<supports>" . ifnull($p["modx_version"], "2.0") . "</supports>";
	if ($selected) {
		echo "<file>";
		echo "<id>deadbeefe00000000000000$i</id>";
		echo "<version>deadbeefe00000000000000$i</version>";
		echo "<filename>" . $p["signature"] . ".zip</filename>";
		echo "<downloads>1</downloads>";
		echo "<lastip>127.0.0.1</lastip>";
		echo "<transport>true</transport>";
		echo "<location>" . $p["location"] . "</location>";
		echo "</file>";
	} else {
		echo "<location>" . $p["location"] . "</location>";
	}
	echo "<signature>" . $p["signature"] . "</signature>";
	echo "<supports_db>" . ifnull($p["modx_db"], "mysql") . "</supports_db>";
	echo "<minimum_supports>" . ifnull($p["modx_version"], "2.0") . "</minimum_supports>";
	echo "<breaks_at>10000000.0</breaks_at>";
	echo "<screenshot>" . ifnull($p["screenshot"], "") . "</screenshot>";
	echo "</package>";
}

$url = $_SERVER["SCRIPT_NAME"];
$url = substr($url, 1, strrpos($url, "/") - 1);
$url = "http://" . $_SERVER["SERVER_NAME"] . "/" . $url;
$path = $_REQUEST["path"];
error_log($path);

$repos = array();
$repos[] = array(
	"name" => "Main"
);

$packages = array();
$packages[] = array(
	"repo" => "Main",	
	"name" => "Test",
	"dir" => "testpackage",
	"version" => "0.0.13-unknown",
	"changelog" => "- Added body to test snippet",
	// "url" => "$url/testpackage.php", // FULL URL for ZIP OR
	// "dir" => "testpackage", // DIR for ZIP
	// "version" => "1.1.1-unknown",
	// "author" => "myloginatforums",
	// "screenshot" => "URL to screenshot",
	// "description" => "Short HTML description",
	// "instruction" => "Short HTML instruction",
	// "changelog" => "Some changelog in HTML",
	// "approved" => "true|false",
	// "audited" => "true|false",
	// "featured" => "true|false",
	// "deprecated" => "true|false",	
	// "license" => "GPLv2",	
	// "modx_version" => "2.0",	
	// "modx_db" => "mysql",	
);

for ($i = 0; $i < count($packages); $i++) {
	$p = &$packages[$i];
	if (!$p["version"]) {
		$p["version"] = "0.0.1-unknown";
	}
	$p["version_major"] = preg_replace("/([0-9]+)\\.([0-9]+)\\.([0-9]+)-([a-zA-Z0-9]+)/i", "\${1}", $p["version"]);
	$p["version_minor"] = preg_replace("/([0-9]+)\\.([0-9]+)\\.([0-9]+)-([a-zA-Z0-9]+)/i", "\${2}", $p["version"]);
	$p["version_patch"] = preg_replace("/([0-9]+)\\.([0-9]+)\\.([0-9]+)-([a-zA-Z0-9]+)/i", "\${3}", $p["version"]);
	$p["version_release"] = preg_replace("/([0-9]+)\\.([0-9]+)\\.([0-9]+)-([a-zA-Z0-9]+)/i", "\${4}", $p["version"]);
	$p["signature"] = strtolower($p["name"] . "-" . $p["version"]);
	$p["location"] = ifnull($p["url"], "$url/download/" . $p["signature"] . ".zip");
}

if ($path == "verify") {
	header("Content-Type: text/xml");
	echo "<status><verified type=\"integer\">1</verified></status>";
} else
if ($path == "welcome") {
	header("Content-Type: text/xml");
	echo "<home>";
	echo "<packages>" . count($packages) . "</packages>";
	echo "<downloads>" . count($packages) . "</downloads>";
	for ($i = 0; $i < count($packages); $i++) {
		$p = $packages[$i];
		echo "<topdownloaded>";
		echo "<id>deadbeefa00000000000000$i</id>";
		echo "<name>" . $p["name"] . "</name>";
		echo "<downloads>1</downloads>";
		echo "</topdownloaded>";
	}
	for ($i = 0; $i < count($packages); $i++) {
		$p = $packages[$i];
		echo "<newest>";
		echo "<id>deadbeefa00000000000000$i</id>";
		echo "<name>" . $p["signature"] . "</name>";
		echo "<package_name>" . $p["name"] . "</package_name>";
		echo "<releasedon>" . strftime("%Y-%m-%dT%H:%M:%SZ", time ()) . "</releasedon>";
		echo "</newest>";
	}
	echo "<url>$url/package</url>";
	echo "</home>";
}  else
if ($path == "repository") {
	header("Content-Type: text/xml");
	$repo = $_REQUEST["repo"];
	if (!$repo) {
		echo "<repositories type=\"array\" of=\"". count($repos) . "\" total=\"". count($repos) . "\" page=\"1\">";
	}
	$found = false;
	for ($i = 0; $i < count($repos); $i++ ) {
		$r = $repos[$i];
		if (!$repo || "deadbeefc00000000000000$i" == $repo) {
			echo "<repository>";
			echo "<rank type=\"integer\">0</rank>";
			echo "<name>". $r["name"] . "</name>";
			echo "<description>". ifnull($r["description"], "No description for this repository") . "</description>";
			echo "<templated type=\"integer\">0</templated>";
			echo "<id>deadbeefc00000000000000$i</id>";
			$cc = 0;
			for ($j = 0; $j < count($packages); $j++) {
				if ($packages[$j]["repo"] == $r["name"]) {
					$cc ++;
				}
			}
			echo "<packages type=\"integer\">$cc</packages>";
			echo "<createdon type=\"datetime\">" . strftime("%Y-%m-%dT%H:%M:%SZ", time ()) . "</createdon>";
			echo "<tag>";
			echo "<id>deadbeefd00000000000000$i</id>";
			echo "<name>All</name>";
			echo "<packages>$cc</packages>";
			echo "<templated>0</templated>";
			echo "</tag>";
			echo "</repository>";
			$found = true;
		}
	}
	if (!$repo) {
		echo "</repositories>";
	}
	if ($repo && !$found) {
		echo "<error><message>No repository found</message></error>";
	}
} else
if ($path == "package") {
	header("Content-Type: text/xml");
	$sig = $_REQUEST["signature"];
	if (!$sig || $update) {
		echo "<packages of=\"" . count($packages) . "\" total=\"" . count($packages) . "\" page=\"1\">";
	}
	$found = false;
	for ($i = 0; $i < count($packages); $i++) {
		$p = $packages[$i];
		if (!$sig || $sig == $p["signature"]) {
			write_package($p, $sig == $p["signature"]);
			$found = true;
		}
	}
	if (!$sig || $update) {
		echo "</packages>";
	}
	if ($sig && !$found) {
		echo "<error><message>No package found</message></error>";
	}
} else
if ($path == "update") {
	header("Content-Type: text/xml");
	$sig = $_REQUEST["signature"];
	$op = array();
	$op["name"] = preg_replace("/([a-zA-Z0-9_]+)-([0-9]+)\\.([0-9]+)\\.([0-9]+)-([a-zA-Z0-9]+)/i", "\${1}", $sig);
	$op["version_major"] = preg_replace("/([a-zA-Z0-9_]+)-([0-9]+)\\.([0-9]+)\\.([0-9]+)-([a-zA-Z0-9]+)/i", "\${2}", $sig);
	$op["version_minor"] = preg_replace("/([a-zA-Z0-9_]+)-([0-9]+)\\.([0-9]+)\\.([0-9]+)-([a-zA-Z0-9]+)/i", "\${3}", $sig);
	$op["version_patch"] = preg_replace("/([a-zA-Z0-9_]+)-([0-9]+)\\.([0-9]+)\\.([0-9]+)-([a-zA-Z0-9]+)/i", "\${4}", $sig);
	$op["version_release"] = preg_replace("/([a-zA-Z0-9_]+)-([0-9]+)\\.([0-9]+)\\.([0-9]+)-([a-zA-Z0-9]+)/i", "\${5}", $sig);	
	$found = false;
	for ($i = 0; $i < count($packages); $i++) {
		$p = $packages[$i];
		if (strtolower($p["name"]) == $op["name"] && $p["signature"] != $sig) {
			$found = true;
			echo "<packages of=\"1\" total=\"1\" page=\"1\">";
			write_package($p, false);
			echo "</packages>";
			break;
		}
	}
	
	if (!$found) {
		echo "<packages of=\"0\" total=\"0\" page=\"0\">";
		echo "</packages>";
	}
} else
if ($path == "download") {
	$sig = $_REQUEST["signature"];
	$debug = $_REQUEST["debug"];
	$getUrl = $_REQUEST["getUrl"];	// Request is only for filename
	if ($getUrl) {
		echo "$url/download/$sig";
		return;
	}
	if (!$sig) {
		echo "<error><message>No package specified</message></error>";
	} else {
		$found = false;
		if (strstr($sig, ".zip") == ".zip") {
			$sig = substr($sig, 0, strlen($sig) - strlen(".zip"));
		}
		for ($i = 0; $i < count($packages); $i++) {
			$p = $packages[$i];
			if ($sig == $p["signature"]) {
				if ($p["url"]) {
					// Forward to other URL
					header("Location: " . $p["url"]);
				} else
				if ($p["dir"]) {
					if ($debug) {
						header("Content-Type: text/xml");
						echo "<files>";
					}
					// Gather all files in zip
					$ff = read_all_files($p["dir"]);
					$files = $ff["files"];
					$z = tempnam("/tmp", "tmp$sig");
					$zip = new ZipArchive();
					if ($zip->open($z, ZIPARCHIVE::OVERWRITE) !== true) {
						die("Can`t open for writing $z!");
					}					
					$haveManifest = false;
					$haveSetupOptions = false;
					foreach($files as $file) {
						$fname = $file;
						$fname = substr($file, strpos($file, $p["dir"]) + strlen($p["dir"]) + 1);
						if ($fname == "manifest.php") {
							$haveManifest = true;
						}
						if ($fname == "setup-options.php") {
							$haveSetupOptions = true;
						}
						$fname = "$sig/$fname";
						$zip->addFile($file, $fname);
						if ($debug) {
							echo "<file realfile='$file'>$fname</file>";
						}
					}
					
					$setupOptions = "";
					$manifest = "";
					$readme = ""; // README.txt
					if (file_exists($p["dir"] . "/README.txt")) {
						$readme = join("", file($p["dir"] . "/README.txt"));
					}
					$license = ""; // LICENSE.txt
					if (file_exists($p["dir"] . "/LICENSE.txt")) {
						$license = join("", file($p["dir"] . "/LICENSE.txt"));
					}
					$changelog = ""; // CHANGELOG.txt
					if (file_exists($p["dir"] . "/CHANGELOG.txt")) {
						$changelog = join("", file($p["dir"] . "/CHANGELOG.txt"));
					}
					
					if (!$haveManifest) {
						$s = '<' . '?php return array(' . "\n";
						$s .= "'manifest-version' => '1.1',\n";
						
						// Attrs
						$s .= "'manifest-attributes' => array(\n";
						$s .= "'license' => '" . addcslashes(ifnull($license, ifnull($p["license"], "GPLv2")), "'") . "',\n";
						$s .= "'readme' => '" . addcslashes(ifnull($readme, ifnull($p["description"], 'No description for this package')), "'") . "',\n";
						$s .= "'changelog' => '" . addcslashes(ifnull($changelog, ifnull($p["changelog"], 'No changelog')), "'") . "',\n";
						$s .= "'setup-options' => '$sig/setup-options.php',\n";
						$s .= "),\n";
						
						$s .= "'manifest-vehicles' => array(\n";
						
						// Generic modCategory vehicle
						$s .= "0 => array(\n";
						$s .= "'vehicle_package' => 'transport',\n";
						$s .= "'vehicle_class' => 'xPDOObjectVehicle',\n";
						$s .= "'class' => 'modCategory',\n";
						$s .= "'guid' => 'deadbeeff00000000000000000000001',\n";
						$s .= "'native_key' => 1,\n";
						$s .= "'filename' => 'modCategory/deadbeeff00000000000000000000000.vehicle',\n";
						$s .= "'namespace' => '" . ifnull($p["namespace"], $p["name"]) . "',\n";
						$s .= "),\n";
						
						// Vehicle body
						$v = '<' . '?php return array(' . "\n";
						$v .= "'unique_key' => 'category',\n";
						$v .= "'preserve_keys' => false,\n";
						$v .= "'update_object' => true,\n";
						
						$v .= "'related_objects' => array(\n";
							$v .= "'Snippets' => array(\n";
								$v .= "'deadbeefi00000000000000000000000' => array(\n";
									$v .= "'unique_key' => 'name',\n";
									$v .= "'preserve_keys' => false,\n";
									$v .= "'update_object' => true,\n";									
									$v .= "'class' => 'modSnippet',\n";
									$v .= "'object' => '{\"id\":0,\"name\":\"TestSnippet\",\"description\":\"\",\"editor_type\":0,\"category\":0,\"cache_type\":0,\"snippet\":\"echo time();\"}',\n";
									$v .= "'guid' => 'deadbeefk00000000000000000000000',\n";
									$v .= "'native_key' => 1,\n";
									$v .= "'signature' => 'deadbeefl00000000000000000000000',\n";
								$v .= "),\n";
							$v .= "),\n";
						$v .= "),\n";
						$v .= "'validate' => NULL,\n";
						$v .= "'vehicle_package' => 'transport',\n";
						$v .= "'vehicle_class' => 'xPDOObjectVehicle',\n";
						$v .= "'guid' => 'deadbeefg00000000000000000000000',\n";										  
						$v .= "'package' => 'modx',\n";
						$v .= "'class' => 'modCategory',\n";
						$v .= "'signature' => 'deadbeefh00000000000000000000000',\n";
						$v .= "'native_key' => 1,\n";
						$v .= "'object' => '{\"id\":1,\"parent\":0,\"category\":\"" . ifnull($p["namespace"], $p["name"]) . "\"}',\n";
						$v .= '); ?' . '>';
						$zip->addFromString("$sig/modCategory/deadbeeff00000000000000000000000.vehicle", $v);
						
						$s .= "),\n";
						
						$s .= '); ?' . '>';
						$manifest = $s;
						$zip->addFromString("$sig/manifest.php", $manifest);
					}
					
					if (!$haveSetupOptions) {
						$s = '<' . '?php' . "\n";
						$s .= '$message = "' . addcslashes(ifnull($p["setupMessage"], "Press Continue to install."), "\\\'\"&\n\r<>")  . '";' . "\n";
						$s .= 'return $message;' . "\n";
						$s .= '?' . '>';
						$setupOptions = $s;
						$zip->addFromString("$sig/setup-options.php", $setupOptions);
					}
					
					if ($debug) {
						if ($haveManifest) {
							echo "<static-manifest/>";
						} else {
							echo "<manifest><![CDATA[" . htmlspecialchars($manifest) . "]]></manifest>";
						}
						
						if ($haveSetupOptions) {
							echo "<static-setup-options/>";
						} else {
							echo "<setup-options><![CDATA[" . htmlspecialchars($setupOptions) . "]]></setup-options>";
						}
					}
					
					$zip->close();
					if (!$debug) {
						header("Content-Type: application/zip");
						header("Content-Disposition: attachment; filename=$sig.zip");
						header("Content-Length: " + filesize($z));
						readfile($z);
						unlink($z);
					} else {
						echo "</files>";
					}
				} else {
					header("Content-Type: text/xml");
					echo "<error><message>No way to read package ZIP</message></error>";
				}
				$found = true;
			}		
		}
		if (!$found) {
			echo "<error><message>No package $sig found</message></error>";
		}
	}
} else {
?>
<h1>Really simple MODx package repository</h1>

This is one page implementation of MODx package repository. This implementation is <u>not complete</u>. 
Everything is setup for simple one tree multiple package handling.

<h2>Relevant MODx REST calls</h2>
<ul>
	<li><a href="verify">verify</a> (called on adding repo)
	<li><a href="home">home</a> (display repo welcome page)
	<li><a href="repository">repository</a> (read repository tree and tags)
	<li><a href="package">package</a> (read packages in repo or by tag)
</ul>
<?php
}
?>