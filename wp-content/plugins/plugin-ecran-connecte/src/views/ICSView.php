<?php

namespace Views;


use WP_User;

/**
 * Class ICSView
 *
 * Display the schedule
 *
 * @package Views
 */
class ICSView extends View
{
	/**
	 * Displays a schedule based on provided ICS data, the title, and user roles.
	 *
	 * @param array $ics_data The ICS data containing events grouped by year, month, and day.
	 * @param string $title The title to display for the schedule.
	 * @param mixed $allDay Whether to display all-day events or filter by specific user roles.
	 *
	 * @return bool A boolean indicating whether the schedule was successfully displayed.
	 */
    public function displaySchedule(array $ics_data, string $title, mixed $allDay): bool {
        $current_user = wp_get_current_user();
        if (isset($ics_data['events'])) {
            $string = '<div class="class-title">' . $title . '</div>';
            $current_study = 0;
            foreach (array_keys((array)$ics_data['events']) as $year) {
                for ($m = 1; $m <= 12; $m++) {
                    $month = $m < 10 ? '0' . $m : '' . $m;
                    if (array_key_exists($month, (array)$ics_data['events'][$year])) {
                        foreach ((array)$ics_data['events'][$year][$month] as $day => $day_events) {
                            // HEADER
                            if ($current_study > 9) {
                                break;
                            }
                            if ($allDay) {
                                if ($day == date('j')) {
                                    $string .= $this->displayStartSchedule($current_user);
                                }
                            } else if (in_array('television', $current_user->roles) || in_array('technicien', $current_user->roles)) {
                                if ($day == date('j')) {
                                    $string .= $this->displayStartSchedule($current_user);
                                }
                            } else {
                                $string .= $this->giveDate($day, $month, $year);
                                $string .= $this->displayStartSchedule($current_user);
                            }
                            foreach ($day_events as $day_event => $events) {
                                foreach ($events as $event) {
                                    // CONTENT
                                    if ($allDay) {
                                        if ($day == date('j')) {
                                            if ($current_study > 9) {
                                                break;
                                            }
                                            if ($this->getContent($event)) {
                                                ++$current_study;
                                                $string .= $this->getContent($event);
                                            }
                                        }
                                    } else {
                                        if (in_array('television', $current_user->roles) || in_array('technicien', $current_user->roles)) {
                                            if ($day == date('j')) {
                                                if ($current_study > 9) {
                                                    break;
                                                }
                                                if ($this->getContent($event)) {
                                                    ++$current_study;
                                                    $string .= $this->getContent($event);
                                                }
                                            }
                                        } else {
                                            if ($current_study > 9) {
                                                break;
                                            }
                                            if ($day == date('j')) {
                                                if ($current_study > 9) {
                                                    break;
                                                }
                                                if ($this->getContent($event)) {
                                                    ++$current_study;
                                                    $string .= $this->getContent($event);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            // FOOTER
                            if (in_array('television', $current_user->roles) || in_array('technicien', $current_user->roles)) {
                                if ($day == date('j')) {
                                    $string .= $this->displayEndSchedule();
                                }
                            } else {
                                $string .= $this->displayEndSchedule();
                            }
                        }
                    }

                }
            }
            // IF NO SCHEDULE
            if ($current_study < 1) {
                return $this->displayNoSchedule($title, $current_user);
            }
        } else {
            return $this->displayNoSchedule($title, $current_user);
        }

        return $string;
    }

	/**
	 * Display the header of the schedule table
	 *
	 * @param WP_User $current_user The current user object, used to determine roles and adjust the table columns.
	 *
	 * @return string The HTML string representing the start of the schedule table.
	 */
    public function displayStartSchedule(WP_User $current_user): string {
        $string = '<div class="table-responsive">
                   	<table class="table tabSchedule">
                    	<thead class="headerTab">
                        	<tr>
                            	<th scope="col" class="text-center">Horaire</th>';
        if (!in_array("technicien", $current_user->roles)) {
            $string .= '<th scope="col" class="text-center" >Cours</th>
                        <th scope="col" class="text-center">Groupe/Enseignant</th>';
        }
        $string .= '<th scope="col" class="text-center">Salle</th>
                 </tr>
              </thead>
           <tbody>';

        return $string;
    }

	/**
	 * Generate a formatted date string for the given day, month, and year
	 *
	 * @param int $day The day of the month
	 * @param int $month The month of the year
	 * @param int $year The year
	 *
	 * @return string The formatted date string
	 */
    public function giveDate(int $day,int $month,int $year): string {
        $day_of_week = $day + 1;

        return '<h2>' . date_i18n('l j F', mktime(0, 0, 0, $month, $day_of_week, $year)) . '</h2>';
    }

	/**
	 * Retrieves content for a specific event based on the day and current time.
	 *
	 * @param array $event An associative array containing event details such as 'deb', 'fin', 'label', 'description', and 'location'.
	 * @param int $day The day of the month to retrieve the content for. Defaults to the current day.
	 *
	 * @return bool|string Returns a formatted string containing the event's schedule information if successful, or false otherwise.
	 */
    public function getContent(array $event, int $day = 0): bool|string {
        if ($day == 0) {
            $day = date('j');
        }

        $time = date("H:i");
        $duration = str_replace(':', 'h', date("H:i", strtotime($event['deb']))) . ' - ' . str_replace(':', 'h', date("H:i", strtotime($event['fin'])));
        if ($day == date('j')) {
            if (date("H:i", strtotime($event['deb'])) <= $time && $time < date("H:i", strtotime($event['fin']))) {
                $active = true;
            } else {
                $active = false;
            }
        }

        if (substr($event['label'], -3) == "alt") {
            $label = substr($event['label'], 0, -3);
        } else {
            $label = $event['label'];
        }
        $description = substr($event['description'], 0, -30);
        if (!(date("H:i", strtotime($event['fin'])) <= $time) || $day != date('j')) {
            $current_user = wp_get_current_user();
            if (in_array("technicien", $current_user->roles)) {
                return $this->displayLineSchedule([$duration, $event['location']], $active);
            } else {
                return $this->displayLineSchedule([$duration, $label, $description, $event['location']], $active);
            }
        }

        return false;
    }

	/**
	 * Display a single line of the schedule
	 *
	 * @param array $datas The data to be displayed in the table row.
	 * @param bool $active Determines if the row should have an active styling.
	 *
	 * @return string The formatted HTML string representing the table row.
	 */
    public function displayLineSchedule(array $datas, bool $active = false): string {
        if ($active) {
            $string = '<tr class="table-success" scope="row">';
        } else {
            $string = '<tr scope="row">';
        }
        foreach ($datas as $data) {
            $string .= '<td class="text-center">' . $data . '</td>';
        }

        return $string . '</tr>';
    }

	/**
	 * Closes the HTML structure initiated for a schedule display.
	 *
	 * @return string HTML structure containing the end tags for table body, table, and div.
	 */
    public
    function displayEndSchedule(): string {
        return '</tbody>
             </table>
          </div>';
    }


	/**
	 * Displays a message indicating no scheduled courses or returns false based on conditions.
	 *
	 * @param string $title The title to display in the message.
	 * @param WP_User $current_user The current user object containing user details and roles.
	 *
	 * @return bool|string HTML message indicating no schedule for users with specific roles or false if conditions are not met.
	 */
    public function displayNoSchedule(string $title, WP_User $current_user): bool|string {
        if (get_theme_mod('ecran_connecte_schedule_msg', 'show') == 'show' && in_array('television', $current_user->roles)) {
            return '<div class="class-title">' . $title . '</div><div class="courstext">Vous n\'avez pas cours !</div>';
        } else if (!in_array('television', $current_user->roles)) {
            return '<div class="class-title">' . $title . '</div><div class="courstext">Vous n\'avez pas cours !</div>';
        } else {
            return false;
        }
    }
}
