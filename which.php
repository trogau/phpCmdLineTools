<?php
/**
 * which.php
 * 
 * A simple PHP implementation of the UNIX 'which' tool. Searches the PATH for requested file. 
 */

$argc = $_SERVER["argc"];
$argv = $_SERVER["argv"];

if ($argc === 1)
{
  print "usage: which [-v] [-x] [ filename ]\n\n";
  print "-v\tverbose mode\n";
  print "-x\texact mode\n";
  exit;
}


for ($x = 1; $x < sizeof($argv); $x++)
{
  if ($argv[$x] === "-v")
  {
    print "Verbose mode enabled.\n";
    $verbose = 1;
  }

  if ($argv[$x] === "-x")
  {
    print "Exact mode enabled.\n";
    $exact = 1;
  }

  if ( ($argv[$x] !== "-v") && ($argv[$x] !== "-x") )
  {
    $search = $argv[$x];
  }
}

$search = strtolower($search);

$path = getenv("PATH");

if (isset($verbose))
{
  print "Searching for: $search\n";
  print "Searching path: $path";
}

$pathArray = explode(";", $path);

foreach($pathArray as $key=>$val )
{
  $val = trim($val);
  if ($val === "") // sometimes we end up with ;; in the PATH so we get an empty string, let's skip it
    continue;

  // FIXME: probably won't work if there are two env vars in one path entry
  $val = preg_replace_callback("/%(.*?)%/", function ($matches) {  return getenv($matches[1]); }, $val);

  $handle = opendir($val);
  if (!$handle)
	  print "Skipping $val, cannot open it.\n";
  else
  {
	  while ($file = readdir($handle))
    {
        $file = strtolower($file);
        $search = trim($search);
        $file = trim($file);

        if ($file === $search)
          print "Found file in : $val (".number_format(filesize($val."\\".$file))." bytes)\n";
        elseif ( (strstr($file, $search)) && (!isset($exact)) )
          print "Found fragment: $file in $val\n";

        //if ($val == "c:\\utils") print $file."($val)\n";
      }
    }

  if ($handle)
	  closedir($handle);
}