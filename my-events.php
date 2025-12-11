<?php
$requireLogin = true;
include "header.php";

// Top Banner Advert
$placementSlug = 'my-events-page-top-banner';
include "listing-banner.php";

$user_id = $buyerId; // current user ID
$registeredEvents = [];

// Fetch registered events via API
$apiUrl = $siteurl . "script/admin.php?action=registered_events&user_id=" . urlencode($user_id);
$eventsData = curl_get_contents($apiUrl);

if ($eventsData !== false) {
    $registeredEvents = json_decode($eventsData, true);
}

// Build event array for FullCalendar
$calendarEvents = [];

if (!empty($registeredEvents) && empty($registeredEvents['error'])) {
    foreach ($registeredEvents as $event) {

        if (!empty($event['all_event_dates_times'])) {
            $dates = explode(',', $event['all_event_dates_times']);

            foreach ($dates as $d) {
                $parts = explode('|', $d);
                $date = trim($parts[0] ?? '');
                $start = trim($parts[1] ?? '');
                $end = trim($parts[2] ?? '');

                if ($date && $start) {
                    $calendarEvents[] = [
                        "title" => $event['event_title'],
                        "start" => $date . "T" . $start,
                        "end"   => $end ? $date . "T" . $end : null,
                        "url"   => $siteurl . "event-details.php?slug=" . urlencode($event['event_slug'])
                    ];
                }
            }
        }
    }
}

?>

<div class="container mt-5 mb-5">
    <div class="col-lg-12">
        <h2 class="mb-4 text-center">Registered Events</h2>

        <!-- Buttons -->
        <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-sm btn-primary me-2" id="listEventsBtn">
                <i class="fa fa-list"></i> List View
            </button>
            <button class="btn btn-sm btn-outline-primary" id="calendarEventsBtn">
                <i class="fa fa-calendar"></i> Calendar View
            </button>
        </div>

        <!-- List View -->
        <div id="eventsListView">
        <?php if (!empty($registeredEvents) && empty($registeredEvents['error'])) { ?>
            <div class="table-responsive">
                <table id="multi-filter-select" class="table table-bordered table-striped" id="events-table">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Event Title</th>
                            <th>Delivery Format</th>
                            <th>Event Dates & Time</th>
                            <th>View</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $sn = 1; foreach ($registeredEvents as $event): ?>
                        <?php
                            $title = $event['event_title'] ?? 'Unknown Event';
                            $slug = $event['event_slug'] ?? '';
                            $deliveryFormat = $event['delivery_format'] ?? 'N/A';

                            $url = $slug ? $siteurl . "event-details.php?slug=" . urlencode($slug) : '#';

                            $datesList = [];
                            if (!empty($event['all_event_dates_times'])) {
                                $tmp_dates = explode(',', $event['all_event_dates_times']);
                                foreach ($tmp_dates as $d) {
                                    $parts = explode('|', $d);
                                    $datesList[] = ($parts[0] ?? '') . ' ' . ($parts[1] ?? '') . ' - ' . ($parts[2] ?? '');
                                }
                            }
                        ?>
                        <tr>
                            <td><?= $sn++ ?></td>
                            <td><?= htmlspecialchars($title) ?></td>
                            <td><?= htmlspecialchars($deliveryFormat) ?></td>
                            <td>
                                <?php if (!empty($datesList)): ?>
                                    <ul class="mb-0">
                                        <?php foreach ($datesList as $dt): ?>
                                            <li><?= htmlspecialchars($dt) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= htmlspecialchars($url) ?>" target="_blank" class="btn btn-sm btn-info">
                                    View Event
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <div class="alert alert-info text-center">You have no registered events yet.</div>
        <?php } ?>
        </div>

        <!-- Calendar View -->
        <div id="eventsCalendarView" style="display:none;">
            <div id="eventsCalendar"></div>
        </div>

        <!-- Pass Events to JS -->
        <script>
            window.registeredEventsCalendarData = <?= json_encode($calendarEvents); ?>;
        </script>
    </div>
</div>

<script>

document.addEventListener("DOMContentLoaded", function () {

    const listView = document.getElementById("eventsListView");
    const calendarView = document.getElementById("eventsCalendarView");
    const listBtn = document.getElementById("listEventsBtn");
    const calBtn = document.getElementById("calendarEventsBtn");

    // Init FullCalendar
    const calendar = new FullCalendar.Calendar(document.getElementById("eventsCalendar"), {
        initialView: "dayGridMonth",
        themeSystem: "bootstrap5",
        height: 650,
        headerToolbar: {
            left: "prev,next today",
            center: "title",
            right: "dayGridMonth,timeGridWeek,timeGridDay"
        },
        events: window.registeredEventsCalendarData,
        eventColor: "#007bff",
        eventTextColor: "#fff"
    });

    // Default list view
    listView.style.display = "block";
    calendarView.style.display = "none";

    listBtn.addEventListener("click", () => {
        listView.style.display = "block";
        calendarView.style.display = "none";

        listBtn.classList.replace("btn-outline-primary", "btn-primary");
        calBtn.classList.replace("btn-primary", "btn-outline-primary");
    });

    calBtn.addEventListener("click", () => {
        listView.style.display = "none";
        calendarView.style.display = "block";

        listBtn.classList.replace("btn-primary", "btn-outline-primary");
        calBtn.classList.replace("btn-outline-primary", "btn-primary");

        setTimeout(() => calendar.render(), 150);
    });

});


</script>
<?php include "footer.php"; ?>
