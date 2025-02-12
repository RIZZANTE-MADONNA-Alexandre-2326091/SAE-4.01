<?php

namespace Controllers;

use Exception;

/**
 * Class Controller
 *
 * Main Controller contain all basics functions
 *
 * @package Controllers
 */
class Controller
{

	/**
	 * Retrieve and clean parts of the URL from the request URI.
	 *
	 * @return array An array of non-empty segments from the request URI.
	 */
    public function getPartOfUrl(): array {
        $url = $_SERVER['REQUEST_URI'];
        $urlExplode = explode('/', $url);
        $cleanUrl = array();
        for ($i = 0; $i < sizeof($urlExplode); ++$i) {
            if ($urlExplode[$i] != '/' && $urlExplode[$i] != '') {
                $cleanUrl[] = $urlExplode[$i];
            }
        }
        return $cleanUrl;
    }

	/**
	 * Add an event to the log file
	 *
	 * @param string $event The log event description
	 *
	 * @return void
	 */
    public function addLogEvent(string $event): void {
        $time = date("D, d M Y H:i:s");
        $time = "[" . $time . "] ";
        $event = $time . $event . "\n";
        file_put_contents(ABSPATH . TV_PLUG_PATH . "fichier.log", $event, FILE_APPEND);
    }

	/**
	 * Generate a URL for an anonymous calendar based on the provided code.
	 *
	 * @param int $code The code representing the resource.
	 *
	 * @return string The generated URL for the calendar.
	 */
    public function getUrl(int $code): string {
        $str = strtotime("now");
        $str2 = strtotime(date("Y-m-d", strtotime('now')) . " +6 day");
        $start = date('Y-m-d', $str);
        $end = date('Y-m-d', $str2);
        $url = 'https://ade-web-consult.univ-amu.fr/jsp/custom/modules/plannings/anonymous_cal.jsp?projectId=8&resources=' . $code . '&calType=ical&firstDate=' . $start . '&lastDate=' . $end;
        return $url;
    }

	/**
	 * Retrieve the file path for a .ics file or download it if not available locally.
	 *
	 * @param int $code Code identifier for the file.
	 *
	 * @return string The file path of the specified .ics file.
	 */
    public function getFilePath(int $code): string {
        $base_path = ABSPATH . TV_ICSFILE_PATH;

        // Check if local file exists
        for ($i = 0; $i <= 3; ++$i) {
            $file_path = $base_path . 'file' . $i . '/' . $code . '.ics';
            if (file_exists($file_path) && filesize($file_path) > 100)
                return $file_path;
        }

        // No local version, let's download one
        $this->addFile($code);
        return $base_path . "file0/" . $code . '.ics';
    }

	/**
	 * Adds a file by retrieving its content from a provided URL and saving it locally.
	 *
	 * @param string $code Unique identifier used to fetch the file and create its path.
	 *
	 * @return void
	 */
    public function addFile(string $code): void {
        try {
            $path = ABSPATH . TV_ICSFILE_PATH . "file0/" . $code . '.ics';
            $url = $this->getUrl($code);
            //file_put_contents($path, fopen($url, 'r'));
            $contents = '';
            if (($handler = @fopen($url, "r")) !== FALSE) {
                while (!feof($handler)) {
                    $contents .= fread($handler, 8192);
                }
                fclose($handler);
            } else {
                throw new Exception('File open failed.');
            }
            if ($handle = fopen($path, "w")) {
                fwrite($handle, $contents);
                fclose($handle);
            } else {
                throw new Exception('File open failed.');
            }
        } catch (Exception $e) {
            $this->addLogEvent($e);
        }
    }

	/**
	 * Determines if a given string represents a valid calendar date.
	 *
	 * @param string $date The date string to validate, formatted as 'YYYY-MM-DD'.
	 *
	 * @return bool Returns true if the date is valid, false otherwise.
	 */
    public function isRealDate(string $date): bool {
        if (false === strtotime($date)) {
            return false;
        }
        list($year, $month, $day) = explode('-', $date);
        return checkdate($month, $day, $year);
    }
}
