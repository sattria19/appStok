<?php











namespace Composer;

use Composer\Autoload\ClassLoader;
use Composer\Semver\VersionParser;






class InstalledVersions
{
private static $installed = array (
  'root' => 
  array (
    'pretty_version' => '1.0.0+no-version-set',
    'version' => '1.0.0.0',
    'aliases' => 
    array (
    ),
    'reference' => NULL,
    'name' => 'acer/app_parfum',
  ),
  'versions' => 
  array (
    'acer/app_parfum' => 
    array (
      'pretty_version' => '1.0.0+no-version-set',
      'version' => '1.0.0.0',
      'aliases' => 
      array (
      ),
      'reference' => NULL,
    ),
    'brick/math' => 
    array (
      'pretty_version' => '0.13.1',
      'version' => '0.13.1.0',
      'aliases' => 
      array (
      ),
      'reference' => 'fc7ed316430118cc7836bf45faff18d5dfc8de04',
    ),
    'chillerlan/php-qrcode' => 
    array (
      'pretty_version' => '5.0.3',
      'version' => '5.0.3.0',
      'aliases' => 
      array (
      ),
      'reference' => '42e215640e9ebdd857570c9e4e52245d1ee51de2',
    ),
    'chillerlan/php-settings-container' => 
    array (
      'pretty_version' => '3.2.1',
      'version' => '3.2.1.0',
      'aliases' => 
      array (
      ),
      'reference' => '95ed3e9676a1d47cab2e3174d19b43f5dbf52681',
    ),
    'composer/pcre' => 
    array (
      'pretty_version' => '3.3.2',
      'version' => '3.3.2.0',
      'aliases' => 
      array (
      ),
      'reference' => 'b2bed4734f0cc156ee1fe9c0da2550420d99a21e',
    ),
    'dompdf/dompdf' => 
    array (
      'pretty_version' => 'v1.2.2',
      'version' => '1.2.2.0',
      'aliases' => 
      array (
      ),
      'reference' => '5031045d9640b38cfc14aac9667470df09c9e090',
    ),
    'maennchen/zipstream-php' => 
    array (
      'pretty_version' => '3.1.1',
      'version' => '3.1.1.0',
      'aliases' => 
      array (
      ),
      'reference' => '6187e9cc4493da94b9b63eb2315821552015fca9',
    ),
    'markbaker/complex' => 
    array (
      'pretty_version' => '3.0.2',
      'version' => '3.0.2.0',
      'aliases' => 
      array (
      ),
      'reference' => '95c56caa1cf5c766ad6d65b6344b807c1e8405b9',
    ),
    'markbaker/matrix' => 
    array (
      'pretty_version' => '3.0.1',
      'version' => '3.0.1.0',
      'aliases' => 
      array (
      ),
      'reference' => '728434227fe21be27ff6d86621a1b13107a2562c',
    ),
    'phenx/php-font-lib' => 
    array (
      'pretty_version' => '0.5.6',
      'version' => '0.5.6.0',
      'aliases' => 
      array (
      ),
      'reference' => 'a1681e9793040740a405ac5b189275059e2a9863',
    ),
    'phenx/php-svg-lib' => 
    array (
      'pretty_version' => '0.4.1',
      'version' => '0.4.1.0',
      'aliases' => 
      array (
      ),
      'reference' => '4498b5df7b08e8469f0f8279651ea5de9626ed02',
    ),
    'phpoffice/phpspreadsheet' => 
    array (
      'pretty_version' => '4.3.1',
      'version' => '4.3.1.0',
      'aliases' => 
      array (
      ),
      'reference' => 'd0ac70d610fb62ccad38c2696569ae1ad42acc70',
    ),
    'psr/http-client' => 
    array (
      'pretty_version' => '1.0.3',
      'version' => '1.0.3.0',
      'aliases' => 
      array (
      ),
      'reference' => 'bb5906edc1c324c9a05aa0873d40117941e5fa90',
    ),
    'psr/http-factory' => 
    array (
      'pretty_version' => '1.0.2',
      'version' => '1.0.2.0',
      'aliases' => 
      array (
      ),
      'reference' => 'e616d01114759c4c489f93b099585439f795fe35',
    ),
    'psr/http-message' => 
    array (
      'pretty_version' => '2.0',
      'version' => '2.0.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '402d35bcb92c70c026d1a6a9883f06b2ead23d71',
    ),
    'psr/simple-cache' => 
    array (
      'pretty_version' => '3.0.0',
      'version' => '3.0.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '764e0b3939f5ca87cb904f570ef9be2d78a07865',
    ),
    'ramsey/collection' => 
    array (
      'pretty_version' => '2.0.0',
      'version' => '2.0.0.0',
      'aliases' => 
      array (
      ),
      'reference' => 'a4b48764bfbb8f3a6a4d1aeb1a35bb5e9ecac4a5',
    ),
    'ramsey/uuid' => 
    array (
      'pretty_version' => '4.8.1',
      'version' => '4.8.1.0',
      'aliases' => 
      array (
      ),
      'reference' => 'fdf4dd4e2ff1813111bd0ad58d7a1ddbb5b56c28',
    ),
    'rhumsaa/uuid' => 
    array (
      'replaced' => 
      array (
        0 => '4.8.1',
      ),
    ),
    'sabberworm/php-css-parser' => 
    array (
      'pretty_version' => 'v8.7.0',
      'version' => '8.7.0.0',
      'aliases' => 
      array (
      ),
      'reference' => 'f414ff953002a9b18e3a116f5e462c56f21237cf',
    ),
  ),
);
private static $canGetVendors;
private static $installedByVendor = array();







public static function getInstalledPackages()
{
$packages = array();
foreach (self::getInstalled() as $installed) {
$packages[] = array_keys($installed['versions']);
}


if (1 === \count($packages)) {
return $packages[0];
}

return array_keys(array_flip(\call_user_func_array('array_merge', $packages)));
}









public static function isInstalled($packageName)
{
foreach (self::getInstalled() as $installed) {
if (isset($installed['versions'][$packageName])) {
return true;
}
}

return false;
}














public static function satisfies(VersionParser $parser, $packageName, $constraint)
{
$constraint = $parser->parseConstraints($constraint);
$provided = $parser->parseConstraints(self::getVersionRanges($packageName));

return $provided->matches($constraint);
}










public static function getVersionRanges($packageName)
{
foreach (self::getInstalled() as $installed) {
if (!isset($installed['versions'][$packageName])) {
continue;
}

$ranges = array();
if (isset($installed['versions'][$packageName]['pretty_version'])) {
$ranges[] = $installed['versions'][$packageName]['pretty_version'];
}
if (array_key_exists('aliases', $installed['versions'][$packageName])) {
$ranges = array_merge($ranges, $installed['versions'][$packageName]['aliases']);
}
if (array_key_exists('replaced', $installed['versions'][$packageName])) {
$ranges = array_merge($ranges, $installed['versions'][$packageName]['replaced']);
}
if (array_key_exists('provided', $installed['versions'][$packageName])) {
$ranges = array_merge($ranges, $installed['versions'][$packageName]['provided']);
}

return implode(' || ', $ranges);
}

throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
}





public static function getVersion($packageName)
{
foreach (self::getInstalled() as $installed) {
if (!isset($installed['versions'][$packageName])) {
continue;
}

if (!isset($installed['versions'][$packageName]['version'])) {
return null;
}

return $installed['versions'][$packageName]['version'];
}

throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
}





public static function getPrettyVersion($packageName)
{
foreach (self::getInstalled() as $installed) {
if (!isset($installed['versions'][$packageName])) {
continue;
}

if (!isset($installed['versions'][$packageName]['pretty_version'])) {
return null;
}

return $installed['versions'][$packageName]['pretty_version'];
}

throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
}





public static function getReference($packageName)
{
foreach (self::getInstalled() as $installed) {
if (!isset($installed['versions'][$packageName])) {
continue;
}

if (!isset($installed['versions'][$packageName]['reference'])) {
return null;
}

return $installed['versions'][$packageName]['reference'];
}

throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
}





public static function getRootPackage()
{
$installed = self::getInstalled();

return $installed[0]['root'];
}







public static function getRawData()
{
return self::$installed;
}



















public static function reload($data)
{
self::$installed = $data;
self::$installedByVendor = array();
}




private static function getInstalled()
{
if (null === self::$canGetVendors) {
self::$canGetVendors = method_exists('Composer\Autoload\ClassLoader', 'getRegisteredLoaders');
}

$installed = array();

if (self::$canGetVendors) {
foreach (ClassLoader::getRegisteredLoaders() as $vendorDir => $loader) {
if (isset(self::$installedByVendor[$vendorDir])) {
$installed[] = self::$installedByVendor[$vendorDir];
} elseif (is_file($vendorDir.'/composer/installed.php')) {
$installed[] = self::$installedByVendor[$vendorDir] = require $vendorDir.'/composer/installed.php';
}
}
}

$installed[] = self::$installed;

return $installed;
}
}
