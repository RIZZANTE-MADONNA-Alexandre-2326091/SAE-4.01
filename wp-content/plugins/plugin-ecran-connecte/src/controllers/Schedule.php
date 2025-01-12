<?php

namespace Controllers;

/**
 * Manage schedules,
 * For display a schedule we use R34ICS
 * Interface Schedule
 */
interface Schedule
{
	/**
	 * Display the schedule based on the provided code and all-day preference
	 *
	 * @param string $code The code identifying the specific schedule
	 * @param bool $allDay Indicates whether to display an all-day schedule
	 *
	 * @return mixed
	 */
    public function displaySchedule(string $code, bool $allDay): mixed;

	/**
	 * Displays the schedule associated with the current user.
	 *
	 * This method retrieves and presents the schedule details
	 * relevant to the user, including events, tasks, or activities.
	 * It ensures the data is properly formatted for display and may
	 * include additional contextual information as needed.
	 */
    public function displayMySchedule();
}
