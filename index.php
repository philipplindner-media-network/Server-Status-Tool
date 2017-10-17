<?php
$sDistroName = '';
$sDistroVer  = '';

    foreach (glob("/etc/*_version") as $filename) 
    {
        list( $sDistroName, $dummy ) = explode( '_', basename($filename) );

        $sDistroName = ucfirst($sDistroName);
        $sDistroVer  = trim( file_get_contents($filename) );
        
        $aCheck['distro'] = "$sDistroName $sDistroVer";
        break;
    }
    
    if( !$aCheck['distro'] )
    {
        if( file_exists( '/etc/issue' ) )
        {
            $lines = file('/etc/issue');
            $aCheck['distro'] = trim( $lines[0] );
        }
        else
        {
            $output = NULL;
            exec( "uname -om", $output );
            $aCheck['distro'] = trim( implode( ' ', $output ) );
        }
    }
    
    $cpu = file( '/proc/cpuinfo' );
    $vendor = NULL;
    $model = NULL;
    $cores = 0;
    foreach( $cpu as $line )
    {
        if( preg_match( '/^vendor_id\s*:\s*(.+)$/i', $line, $m ) )
        {
            $vendor = $m[1];
        }
        else if( preg_match( '/^model\s+name\s*:\s*(.+)$/i', $line, $m ) )
        {
            $model = $m[1];
        }
        else if( preg_match( '/^processor\s*:\s*\d+$/i', $line ) )
        {
            $cores++;
        }
    }
    
    $aCheck['cpu']    = "$vendor, $model";
    $aCheck['cores']  = $cores;
    $aCheck['kernel'] = trim(file_get_contents("/proc/version"));

	
function sec2human($time) {
  $seconds = $time%60;
	$mins = floor($time/60)%60;
	$hours = floor($time/60/60)%24;
	$days = floor($time/60/60/24);
	
	$expord=$days ." (".$hours.":".$mins.":".$seconds.")";
	return $expord;
}

$array = array();
$fh = fopen('/proc/uptime', 'r');
$uptime = fgets($fh);
fclose($fh);
$uptime = explode('.', $uptime, 2);
$aCheck['uptime'] = sec2human($uptime[0]);

$fh = fopen('/proc/meminfo', 'r');
  $mem = 0;
  while ($line = fgets($fh)) {
    $pieces = array();
    if (preg_match('/^MemTotal:\s+(\d+)\skB$/', $line, $pieces)) {
      $memtotal = $pieces[1];
    }
    if (preg_match('/^MemFree:\s+(\d+)\skB$/', $line, $pieces)) {
      $memfree = $pieces[1];
    }
    if (preg_match('/^Cached:\s+(\d+)\skB$/', $line, $pieces)) {
      $memcache = $pieces[1];
      break;
    }
  }
fclose($fh);

$memmath = $memcache + $memfree;
$memmath2 = $memmath / $memtotal * 100;
$memory = round($memmath2) . '';

if ($memory >= "51%") { $memlevel = "Gut"; }
elseif ($memory <= "50%") { $memlevel = "Warnung"; }
elseif ($memory <= "35%") { $memlevel = "!!ACHTUNG!!"; }

$aCheck['memory']=$memory ."-".$memlevel; 

$hddtotal = disk_total_space("/");
$hddfree = disk_free_space("/");
$hddmath = $hddfree / $hddtotal * 100;
$hdd = round($hddmath) . '';

if ($hdd >= "51%") { $hddlevel = "Gut"; }
elseif ($hdd <= "50%") { $hddlevel = "Warnug"; }
elseif ($hdd <= "35%") { $hddlevel = "!!ACHTUNG!!"; }

$aCheck['hdd']=$hdd ."-".$hddlevel;
$load = sys_getloadavg();
$aCheck['load'] = $load[0];
$aCheck['online']="100%";

//ping tool
function ping($host, $port, $timeout) 
{ 
  $tB = microtime(true); 
  $fP = fSockOpen($host, $port, $errno, $errstr, $timeout); 
  if (!$fP) { return "down"; } 
  $tA = microtime(true); 
  return round((($tA - $tB) * 1000), 0)." ms"; 
}
$aCheck['ping']=ping("www.google.com", 80, 10);


//LS Sensors
$fan=		shell_exec("sensors | grep fan");
$temp=		shell_exec("sensors | grep temp");
$core=		shell_exec("sensors | grep Core");
//--
$vcore= 	shell_exec("sensors | grep Vcore");
$AVCC=		shell_exec("sensors | grep AVCC");
$_33v=		shell_exec("sensors | grep +3.3V");
$in=		shell_exec("sensors | grep in");
$_3VSB=		shell_exec("sensors | grep 3VSB");
$Vbat=		shell_exec("sensors | grep Vbat");
$cpu0_vid=	shell_exec("sensors | grep cpu0_vid");

//-- HDD TEMP

$hdd1=file_get_contents("/var/www/html/sst/hdd1.txt");
$hdd2=file_get_contents("/var/www/html/sst/hdd2.txt");
$hdd3=file_get_contents("/var/www/html/sst/hddteim.txt");
$hddtamp_all=$hdd1."\n--\n".$hdd2."\n--\n".$hdd3;
//echo $hddtamp_all;

	



//Test Ausgabe 
//print_r($aCheck);

//XML Auslagerung
header("Content-type: text/xml");
echo"<server>
\t<os>".$aCheck['distro']."</os>
\t<cpu>".$aCheck['cpu']."</cpu>
\t<coros>".$aCheck['cores']."</coros>
\t<kernel>".$aCheck['kernel']."</kernel>
\t<uptime>".$aCheck['uptime']."</uptime>
\t<memory>".$aCheck['memory']."</memory>
\t<hdd>".$aCheck['hdd']."</hdd>
\t<load>".$aCheck['load']."</load>
\t<online>".$aCheck['online']."</online>
\t<ping>".$aCheck['ping']."</ping>
\t<sensors>
\t\t<fan>".$fan."</fan>
\t\t<temp>".$temp."</temp>
\t\t<core>".$core."</core>
\t\t<vcore>".$vcore."</vcore>
\t\t<AVCC>".$AVCC."</AVCC>
\t\t<x33v>".$_33v."</x33v>
\t\t<in>".$in."</in>
\t\t<x3VSB>".$_3VSB."</x3VSB>
\t\t<Vban>".$Vbat."</Vban>
\t\t<cpu0vid>".$cpu0_vid."</cpu0vid>
\t\t<hddtemp>".$hddtamp_all."</hddtemp>
\t</sensors>
</server>";

?>
