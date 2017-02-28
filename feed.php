<!DOCTYPE  html>
<head>


    <meta charset="UTF-8">
    <meta name="description"
          content="Convert Monash and import monash allocate plus calendar to other calendar software">
    <meta name="keywords"
          content="Allocate+ to .ics/ical, convert monash timetable to google calendar, allocate plus to ics ical">
    <meta name="author" content="Ishan Joshi">


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css"
          integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
            integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
            crossorigin="anonymous"></script>


    <script>
        (function (i, s, o, g, r, a, m) {
            i['GoogleAnalyticsObject'] = r;
            i[r] = i[r] || function () {
                    (i[r].q = i[r].q || []).push(arguments)
                }, i[r].l = 1 * new Date();
            a = s.createElement(o),
                m = s.getElementsByTagName(o)[0];
            a.async = 1;
            a.src = g;
            m.parentNode.insertBefore(a, m)
        })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');

        ga('create', 'UA-91540034-1', 'auto');
        ga('send', 'pageview');

    </script>

    <script>
        //ics formatter libarary :)
        //https://github.com/matthiasanderer/icsFormatter
        var icsFormatter = function () {
            'use strict';

            if (navigator.userAgent.indexOf('MSIE') > -1 && navigator.userAgent.indexOf('MSIE 10') == -1) {
                //console.log('Unsupported Browser');
                return;
            }

            var SEPARATOR = (navigator.appVersion.indexOf('Win') !== -1) ? '\r\n' : '\n';
            var calendarEvents = [];
            var calendarStart = [
                'BEGIN:VCALENDAR',
                'VERSION:2.0'
            ].join(SEPARATOR);
            var calendarEnd = SEPARATOR + 'END:VCALENDAR';

            return {
                /**
                 * Returns events array
                 * @return {array} Events
                 */
                'events': function () {
                    return calendarEvents;
                },

                /**
                 * Returns calendar
                 * @return {string} Calendar in iCalendar format
                 */
                'calendar': function () {
                    return calendarStart + SEPARATOR + calendarEvents.join(SEPARATOR) + calendarEnd;
                },

                /**
                 * Add event to the calendar
                 * @param  {string} subject     Subject/Title of event
                 * @param  {string} description Description of event
                 * @param  {string} location    Location of event
                 * @param  {string} begin       Beginning date of event
                 * @param  {string} stop        Ending date of event
                 */
                'addEvent': function (subject, description, location, begin, stop) {
                    // I'm not in the mood to make these optional... So they are all required
                    if (typeof subject === 'undefined' ||
                        typeof description === 'undefined' ||
                        typeof location === 'undefined' ||
                        typeof begin === 'undefined' ||
                        typeof stop === 'undefined'
                    ) {
                        return false;
                    }

                    //TODO add time and time zone? use moment to format?
                    var start_date = new Date(begin);
                    var end_date = new Date(stop);

                    var start_year = ("0000" + (start_date.getFullYear().toString())).slice(-4);
                    var start_month = ("00" + ((start_date.getMonth() + 1).toString())).slice(-2);
                    var start_day = ("00" + ((start_date.getDate()).toString())).slice(-2);
                    var start_hours = ("00" + (start_date.getHours().toString())).slice(-2);
                    var start_minutes = ("00" + (start_date.getMinutes().toString())).slice(-2);
                    var start_seconds = ("00" + (start_date.getMinutes().toString())).slice(-2);

                    var end_year = ("0000" + (end_date.getFullYear().toString())).slice(-4);
                    var end_month = ("00" + ((end_date.getMonth() + 1).toString())).slice(-2);
                    var end_day = ("00" + ((end_date.getDate()).toString())).slice(-2);
                    var end_hours = ("00" + (end_date.getHours().toString())).slice(-2);
                    var end_minutes = ("00" + (end_date.getMinutes().toString())).slice(-2);
                    var end_seconds = ("00" + (end_date.getMinutes().toString())).slice(-2);

                    // Since some calendars don't add 0 second events, we need to remove time if there is none...
                    var start_time = '';
                    var end_time = '';
                    if (start_minutes + start_seconds + end_minutes + end_seconds !== 0) {
                        start_time = 'T' + start_hours + start_minutes + start_seconds;
                        end_time = 'T' + end_hours + end_minutes + end_seconds;
                    }

                    var start = start_year + start_month + start_day + start_time;
                    var end = end_year + end_month + end_day + end_time;

//                    var a = document.getElementById("clayton-addresses").checked;
                    var a = true;
                    if (a) {
                        console.log("melbourne zone forced");
                        var calendarEvent = [
                            'BEGIN:VEVENT',
                            'CLASS:PUBLIC',
                            'DESCRIPTION:' + description,
                            'DTSTART;TZID=Australia/Melbourne:' + start,
                            'DTEND;TZID=Australia/Melbourne:' + end,
                            'LOCATION:' + location,
                            'SUMMARY;LANGUAGE=en-us:' + subject,
                            'TRANSP:TRANSPARENT',
                            'END:VEVENT'
                        ].join(SEPARATOR);
                    } else {
                        console.log("auto time select");
                        var calendarEvent = [
                            'BEGIN:VEVENT',
                            'CLASS:PUBLIC',
                            'DESCRIPTION:' + description,
                            'DTSTART:' + start,
                            'DTEND:' + end,
                            'LOCATION:' + location,
                            'SUMMARY;LANGUAGE=en-us:' + subject,
                            'TRANSP:TRANSPARENT',
                            'END:VEVENT'
                        ].join(SEPARATOR);
                    }

                    calendarEvents.push(calendarEvent);
                    return calendarEvent;
                },

                /**
                 * Download calendar using the saveAs function from filesave.js
                 * @param  {string} filename Filename
                 * @param  {string} ext      Extention
                 */
                'download': function (filename, ext) {
                    if (calendarEvents.length < 1) {
                        return false;
                    }

                    ext = (typeof ext !== 'undefined') ? ext : '.ics';
                    filename = (typeof filename !== 'undefined') ? filename : 'calendar';
                    var calendar = calendarStart + SEPARATOR + calendarEvents.join(SEPARATOR) + calendarEnd;
                    if (document.getElementById("generate-ics").checked) {
                        window.open("data:text/calendar;charset=utf8," + escape(calendar));
                    }
                    return calendar;
                }
            };
        };

        if (typeof define === "function" && define.amd) {
            /* AMD Format */
            define("icsFormatter", [], function () {
                return icsFormatter();
            });
        } else if (typeof module === "object" && module.exports) {
            /* CommonJS Format */
            module.exports = icsFormatter();
        } else {
            /* Plain Browser Support */
            this.myModule = icsFormatter();
        }
    </script>
    <script>

        function make() {
            var calEntry = icsFormatter();
            text = document.getElementById("csvValues").value;

            if (text.length < 50) {
                alert("Enter valid data");
            }
            else {

                var data = text.split('\n');
                console.log(data.length);
                for (i = 1; i < data.length; i++) {

                    var subjectCode, desc, group, activity, day, time, campus, location, staff, duration, dates;
                    var arraydata = data[i].split(',');

                    //fetch all variables of a row
                    subjectCode = arraydata[0];
                    desc = arraydata[1];
                    group = arraydata[2];
                    activity = arraydata[3];
                    day = arraydata[4];
                    time = arraydata[5];
                    campus = arraydata[6];
                    location = arraydata[7];
                    staff = arraydata[8];
                    duration = arraydata[9];
                    dates = arraydata[10];

                    //constants
                    var tab = "\t";
                    var newline = "\n";

                    var title = subjectCode + tab + desc + tab + group + activity;
                    var place = location.substr(0, location.indexOf("/"));


                    var finalLoc = "";

                    var claytonStreetNames = {
                        All: "Alliance Lane",
                        Anc: "Ancora Imparo Way",
                        Chn: "Chancellors Walk",
                        Col: "College Walk",
                        Exh: "Exhibition Walk",
                        FGR: "Ferntree Gully Road",
                        Inn: "Innovation Walk",
                        Rnf: "Rainforest Walk",
                        Res: "Research Way",
                        Sce: "Scenic Boulevard"
                    };

                    var locationReplacements = [
                        [/Exh\w\//, "Exh/"]
                    ];
                    //CREDITS TO MARK'S WEBSITE AT : https://mcpower.github.io/icallate/

                    var splitLocation = location.split("/");
                    if (splitLocation[0].startsWith("CL_") && claytonStreetNames.hasOwnProperty(splitLocation[0].substr(-3, 3))) {
                        location = "Room " + splitLocation[1] + ", " + splitLocation[0].slice(3, -3) + " " + claytonStreetNames[splitLocation[0].substr(-3, 3)];
                    }
                    finalLoc = location;

                    var year, month, date, hours, minutes, seconds, milliseconds;
                    seconds = 0;
                    milliseconds = 0;

                    //TODO update year based on date rather than fix definition
                    year = 2017;

                    var lessionDates = dates.split(" ");
                    for (q = 0; q < lessionDates.length; q++) {

                        if (lessionDates[q].indexOf("-") >= 0) {
                            var range = lessionDates[q].split('-');
                            var d, m;
                            var s = range[0].split("/");

                            //('0' + deg).slice(-2) - stackoverflow

                            d = ('0' + s[0]).slice(-2);
                            m = ('0' + s[1]).slice(-2);

                            var tData = time.split(":");

                            hours = tData[0];
                            minutes = tData[1];
                            var periodBegin = new Date(year, m - 1, d, hours, minutes, 0, 0);

                            s = range[1].split("/");

                            var d2, m2;
                            d2 = ('0' + s[0]).slice(-2);
                            m2 = ('0' + s[1]).slice(-2);
                            var periodEnd = new Date(year, m2 - 1, d2, hours, minutes, 0, 0);

                            console.log(periodBegin + "\n" + periodEnd);

                            while (periodBegin.getTime() <= periodEnd.getTime()) {
                                var dur = duration.getNums();
                                //console.log(dur[0]);
                                var endTime = new Date(periodBegin.getTime() + dur[0] * 60 * 60 * 1000);

                                var description = campus + " " + desc;

                                //console.log(title + description + finalLoc);
                                calEntry.addEvent(title, description, finalLoc, periodBegin.toUTCString(), endTime.toUTCString());
                                periodBegin = new Date(periodBegin.getFullYear(), periodBegin.getMonth(), periodBegin.getDate() + 7, periodBegin.getHours(), periodBegin.getMinutes(), 0, 0);
                            }
                        }
                        else {
                            s = lessionDates[q].split("/");
                            d2 = ('0' + s[0]).slice(-2);
                            m2 = ('0' + s[1]).slice(-2);

                            var tData = time.split(":");

                            hours = tData[0];
                            minutes = tData[1];
                            periodBegin = new Date(year, m2 - 1, d2, hours, minutes, 0, 0);

                            var dur = duration.getNums();
                            //console.log(dur[0]);
                            var endTime = new Date(periodBegin.getTime() + dur[0] * 60 * 60 * 1000);

                            var description = campus + " " + desc;

                            console.log(title + description + finalLoc);
                            calEntry.addEvent(title, description, finalLoc, periodBegin.toUTCString(), endTime.toUTCString());

                        }

                    }
                }
                localStorage.setItem('last', new Date().toDateString());
            }
//TODO enable this download button
            var ical = calEntry.download('MonashTT', 'ics');

            $.post("icses/makeics.php", {ics: ical})
                .done(function (data) {
                    if (data == 'NONGENERATE')
                        alert("Couldn't generate link");
                    else {
//                        alert( "Data Loaded: " + data );
                        localStorage.setItem('link', "http://139.59.98.45/icsFormatter/icses/" + data);
                        $(".eLink").text('http://139.59.98.45/icsFormatter/icses/' + data);
                    }
                });
//            localStorage.setItem('last', new Date().toDateString());

            $("#last_created").text(localStorage.getItem('last'));

            activaTab("");
            //Do
        }
        //testing commit

        function activaTab(tab) {
            if (tab == '')
            //$('.nav-tabs a[href="#' + tab + '"]').tab('show');
                $('#myModal').modal('show');
            else
                $('.nav-tabs a[href="#' + tab + '"]').tab('show');
        }
        //changed

        String.prototype.getNums = function () {
            var rx = /[+-]?((\.\d+)|(\d+(\.\d+)?)([eE][+-]?\d+)?)/g,
                mapN = this.match(rx) || [];
            return mapN.map(Number);
        };

        function checkMobile() {
            var check = false;
            (function (a) {
                if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino|android|ipad|playbook|silk/i.test(a) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0, 4))) check = true;
            })(navigator.userAgent || navigator.vendor || window.opera);
            if (check) {
                alert("Laptop/Desktop are recommended for this activity");
            }
            if (check) {//TODO check this condition, changed for testing purposes
                //TODO make it show rather than hide
                $("#mobile-alert").show();
            }
        }

        function bodyLoad() {
            checkMobile();
            if (localStorage.getItem('last') != null)
                $("#last_created").text(("Your last file was created on: " + localStorage.getItem('last')));
            if (localStorage.getItem('link') != null) {
                $(".eLink").text(localStorage.getItem(('link')));
            }
            window.onbeforeunload = null;
        }
    </script>
    <style>
        .help_images {
            width: 95%;
            height: 650px;
        }

        .tab-pane {
            overflow-y: hidden;
        }

        #profile_pic {
            width: 100%;
            height: 350px;
        }

        input[type=checkbox] {
            /* Double-sized Checkboxes */
            -ms-transform: scale(2); /* IE */
            -moz-transform: scale(2); /* FF */
            -webkit-transform: scale(2); /* Safari and Chrome */
            -o-transform: scale(2); /* Opera */
            padding: 10px;
        }

        .eLink {
            color: red;
            font-size: 1em;
        }
    </style>
</head>
<body onload="bodyLoad()" style="width: 98%;">
<div style="display: none" id="mobile-alert" class="alert alert-danger alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
    </button>
    <strong>ALERT</strong>
    <p>
        Laptops/Desktops are recommended for this activity. We've detected you're using a mobile device.
    </p>
</div>

<div class="alert alert-info" role="alert">
    <h3>Several Bug Fixes</h3>
    <ul>
        <li>Time remains consistent after daylight savings ends</li>
        <li>Fixed the 'missing weeks' that weren't added</li>
    </ul>
    <br>
    <strong>Please remake your .ics file if you've done it before 25/02/2017</strong>
    <p id="last_created"></p>
    <br>
    <p class="eLink"></p>
    <p>"GET A COPY" DOESN'T WORK ON MICROSOFT EDGE or INTERNET EXPLORER</p>
</div>


<div class="row" style="width: 98%">
    <div class="col-md-1"></div>
    <div class="col-md-8"><h1>Allocate+ to Calendar</h1>
        <br>
        <iframe
            src="https://www.facebook.com/plugins/share_button.php?href=http%3A%2F%2Fishanjoshi.me%2FicsFormatter%2Fics.html&layout=box_count&size=large&mobile_iframe=true&width=72&height=58&appId"
            width="30%" height="100px" style="border:none;overflow:hidden" scrolling="no" frameborder="0"
            allowTransparency="true"></iframe>
    </div>

</div>


<div class="row">
    <div class="col-md-1"></div>
    <div class="col-md-4">
        <h2>Information</h2>
        <ul>
            <li>
                <h3>What it does</h3>
                <p>Converts your Allocate+ Monash timetable to an .ics file which can be used with calendar software</p>
            </li>
            <li>
                <h3>Why it is useful</h3>
                <p>Have your timetable directly into your calendar software. Reduce the need to go into Allocate+ every
                    now and then</p>
            </li>
            <li>
                <h3>Useful external links</h3>
                <ul>
                    <li>
                        <a href="https://support.google.com/calendar/answer/37118?hl=en">
                            Import .ics file to Google Calendar
                        </a>
                    </li>
                    <li>
                        <a href="https://www.lifewire.com/how-to-import-ics-calendar-files-in-ical-1172177">
                            Import .ics file into iCal (Apple Users) software
                        </a>
                    </li>
                    <li>
                        <a href="https://www.gsa.gov/portal/mediaId/122190/fileName/ImportCalendarGoogle.action">
                            Add calendar by URL
                        </a>
                    </li>
                    <!--                    https://www.gsa.gov/portal/mediaId/122190/fileName/ImportCalendarGoogle.action-->
                </ul>
            </li>
        </ul>
        <h2>Tool</h2>
        <textarea style="height: 12em; width: 100%" placeholder="Copy Paste your csv file" id="csvValues"></textarea>
        <br>
        <!--<div class="checkbox">
            <label>
                <input size="20px" type="checkbox" value="" id="clayton-addresses">
                Force Melbourne Time-zone (if not checked it'll use your default timezone)
            </label>
        </div>
        <br><br>-->
        <div class="checkbox">
            <label>
                <input size="20px" type="checkbox" value="" id="generate-ics">
                Get a copy
            </label>
        </div>
        <button style="width: 100%; height: 40px" id="submit" onclick="make()"
                data-toggle="modal" data-target="#feedbackModal">
            Generate Link
        </button>
    </div>
    <div class="col-md-7">
        <div>

            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#step_1" aria-controls="home" role="tab"
                                                          data-toggle="tab">Step 1</a></li>
                <li role="presentation"><a href="#step_2" aria-controls="profile" role="tab" data-toggle="tab">Step
                        2</a></li>
                <li role="presentation"><a href="#step_3" aria-controls="messages" role="tab" data-toggle="tab">Step
                        3</a></li>
                <li role="presentation"><a href="#feedback" aria-controls="feedback" role="tab" data-toggle="tab">Feedback</a>
                </li>
                <li role="presentation"><a href="#about" aria-controls="about" role="tab" data-toggle="tab">About Me</a>
                </li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="step_1">
                    <!--Step 1-->
                    <ul>
                        <li>
                            <h2>
                                Open
                                <a href="http://www.monash.edu/timetables/login.html" target="_blank">
                                    Allocate+
                                </a>
                                and login
                            </h2>
                            <img class="help_images" src="img/steps/1.JPG">
                        </li>
                        <li>
                            <h2>Click the <span class="glyphicon glyphcon-download">Download</span> button
                            </h2>
                            <img class="help_images" src="img/steps/2.JPG">
                        </li>
                    </ul>
                </div>
                <div role="tabpanel" class="tab-pane" id="step_2">
                    <ul>
                        <li>
                            <h2>Open the Excel File</h2>
                            <p>Open the excel file and get rid of all existing commas using 'find and replace'(<kbd>Control
                                    + F</kbd>). <br>
                                Replace it with NO spaces.</p>
                            <img class="help_images" src="img/steps/3.JPG">
                        </li>
                        <li>
                            <h2>Save as a <kbd>.csv</kbd> file</h2>
                            <p>
                                Save it as a CSV file and click yes to all warnings
                            </p>
                            <img class="help_images" src="img/steps/4.JPG">
                        </li>
                        <li>
                            <h2>Open <kbd>.csv</kbd> file in Text Editor</h2>
                            <ul>
                                <li>
                                    Open the csv file using notepad or notepad++
                                </li>
                                <li>
                                    <p>
                                        Copy it's contents and paste in the textbox on the website. <br>
                                        Then press the
                                    </p>
                                </li>
                            </ul>

                            <div class="alert alert-warning" role="alert">
                                DO NOT INCLUDE THE BLANK LAST LINE
                            </div>
                            <img class="help_images" src="img/steps/5.JPG">
                        </li>
                    </ul>
                </div>
                <div role="tabpanel" class="tab-pane" id="step_3">
                    <ul>
                        <li>
                            <h2>Copy the link shown in red</h2>
                            <img class="help_images" src="img/steps/6.JPG">
                        </li>
                        <li>
                            <h2>Open Google Calendar</h2>
                            <ul>
                                <li>Open Google Calendar in Laptop/Desktop</li>
                                <li>Locate the "Add by URL" option as shown</li>
                                <li>Click "Add by URL"</li>
                            </ul>
                            <img class="help_images" src="img/steps/7.JPG">
                        </li>
                        <li>
                            <h2>Paste the link generated</h2>
                            <img class="help_images" src="img/steps/8.JPG">
                        </li>
                    </ul>
                </div>
                <div role="tabpanel" class="tab-pane" id="feedback">
                    <iframe
                        id="feedback_form"
                        style="overflow-y: hidden"
                        src="https://docs.google.com/a/student.monash.edu/forms/d/e/1FAIpQLSetWParibqSuwUHsOwcPDl3AC1Z7IsZpsshdqqqyT9ApaOpPA/viewform?embedded=true"
                        width="98%" height="600px" frameborder="0" marginheight="0" marginwidth="0">Loading...
                    </iframe>
                </div>
                <div role="tabpanel" class="tab-pane" id="about">
                    <div class="row" style="width: 98%">
                        <div class="col-md-1"></div>
                        <div class="col-md-4">
                            <img src="img/prof/ishan.jpg" id="profile_pic">
                        </div>
                        <div class="col-md-6">
                            <h2>Ishan Joshi</h2>
                            <p>
                                I'm currently studying Computer Science at Monash University in Australia. Programming
                                is my passion and I like making things easier to use :)
                                <br><br>
                                Apart from studying I'm into playing cricket, photography and trying out everything I
                                can...
                                <br><br>
                            </p>
                            <blockquote class="blockquote">
                                <p class="mb-0">
                                    When things are falling apart, they may be falling into place!!
                                </p>
                            </blockquote>
                            <ul>
                                <li>
                                    <span class="glyphicon glyphicon-envelope"></span>:
                                    ishan@ishanjoshi.me
                                </li>
                                <li>
                                    <span class="glyphicon glyphicon-map-marker"></span>:
                                    Melbourne, Australia
                                </li>
                                <li>
                                    <span class="glyphicon glyphicon-map-marker"></span>:
                                    github.com/ish-joshi/
                                </li>
                            </ul>
                        </div>
                    </div>
                    <h2>New Features</h2>
                    <iframe
                        src="https://docs.google.com/forms/d/e/1FAIpQLSeNhatOhDoSVMQTQGsMcQ3CD-NVs2ZP_HhEmq6IoYOx_gkMXg/viewform?embedded=true"
                        width="95%" height="500" frameborder="0" marginheight="0" marginwidth="0">Loading...
                    </iframe>
                </div>
            </div>

        </div>
    </div>
</div>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Please provide feedback</h4>
            </div>
            <div class="modal-body">
                <p class="eLink"></p>
                <iframe
                    style="overflow-y: hidden"
                    src="https://docs.google.com/a/student.monash.edu/forms/d/e/1FAIpQLSetWParibqSuwUHsOwcPDl3AC1Z7IsZpsshdqqqyT9ApaOpPA/viewform?embedded=true"
                    width="98%" height="600px" frameborder="0" marginheight="0" marginwidth="0">Loading...
                </iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" onclick="activaTab('about')">Close
                </button>
            </div>
        </div>
    </div>
</div>


</body>

