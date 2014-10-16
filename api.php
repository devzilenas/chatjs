<? 
/**
 * @author Marius Žilėnas <mzilenas@gmail.com>
 * @copyright 2013 Marius Žilėnas
 */
include 'includes.php';

date_default_timezone_set(Config::TZ);

/**
 * Start buffering.
 */
ob_start();
DB::connect(); 
Login::CheckLogin();
ChatManager::checkTalker();
ob_end_clean(); //Must have clean output for api

$api = TRUE;

/**
 * Collect api output.
 */
ob_start();
Req::process($api);
ob_flush();

