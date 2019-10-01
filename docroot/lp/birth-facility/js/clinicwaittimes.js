// JavaScript source code


(function ($) {
    $(document).ready(function () {
        function waitTimes() {
            function onQuerySucceeded(sender, args) {
                var listItemInfo = '<div class="wait-summary">';
                listItemInfo += '<div class="wait-summary-header">Urgent Care Wait Times</div>';
                listItemInfo += '<div class="wait-summary-links">';
                var listItemEnumerator = items.getEnumerator();
                var clinics = [];
                while (listItemEnumerator.moveNext()) {
                    var oListItem = listItemEnumerator.get_current();
                    clinics.push({
                        waitTime: oListItem.get_item('Wait_x0020_Time'),
                        clinicUrl: oListItem.get_item('tq4m'),
                        title: oListItem.get_item('Title')
                    });
                }
                clinics.sort(function(a,b) {
                    var nameA=a.title.toLowerCase();
                    nameB=b.title.toLowerCase();
                    if (nameA < nameB) //sort string ascending
                        return -1;
                    if (nameA > nameB)
                        return 1;
                    return 0; //default return value (no sorting)
                });
                for (var i = 0; i < clinics.length; i++){
                    var intervalString;
                    var interval = clinics[i].waitTime;
                    if (isNaN(parseInt(interval))) {
                        if (interval == "Disabled") {
                            continue;
                        }
                        else {
                            intervalString = interval;
                        }
                    }
                    else {
                        if (interval == 0) {
                            intervalString = 'No Wait';
                        }
                        else {
                            var hours = Math.floor(interval / 60);
                            var minutes = interval % 60;
                            var hourstring = hours == 0 ? '' : hours == 1 ? '1 hr' : hours.toString() + ' hrs';
                            var minutesstring = minutes == 0 ? '' : (' ' + (minutes < 10 ? '0' : '') + minutes.toString() + ' mins');
                            intervalString = hourstring + minutesstring;
                        }
                    }
                    listItemInfo += '<div class="wait-clinic">';
                    listItemInfo += '<div class="wait-clinic-wait">';
                    listItemInfo += intervalString;
                    listItemInfo += '</div>';
                    listItemInfo += '<div class="wait-clinic-link">';
                    listItemInfo += '<a href="';
                    listItemInfo += clinics[i].clinicUrl;
                    listItemInfo += '">';
                    listItemInfo += clinics[i].title;
                    listItemInfo += '</a>';
                    listItemInfo += '</div>';
                    listItemInfo += '</div>';
                }
                listItemInfo += '</div>';
                listItemInfo += '</div>';
                $(".clinic-summary").html(listItemInfo);
            }
            function onQueryFailed(sender, args) {
            }
            var clientContext = new SP.ClientContext.get_current();
            var website = clientContext.get_site();
            var rootWeb = website.get_rootWeb();
            var lists = rootWeb.get_lists();
            var list = lists.getByTitle("Clinic Wait Times");
            var items = list.getItems(new SP.CamlQuery());
            clientContext.load(items);
            clientContext.executeQueryAsync(Function.createDelegate(this, onQuerySucceeded), Function.createDelegate(this, onQueryFailed));
        }

        function waitClinic() {
            function onQuerySucceeded(sender, args) {
                var listItemEnumerator = items.getEnumerator();
                var clinic;
                while (listItemEnumerator.moveNext()) {
                    var oListItem = listItemEnumerator.get_current();
                    if ($('.wait-box').data('url') == oListItem.get_item('tq4m')) {
                        clinic = {
                            waitTime: oListItem.get_item('Wait_x0020_Time'),
                            clinicUrl: oListItem.get_item('tq4m'),
                            title: oListItem.get_item('Title'),
                            modified: oListItem.get_item('Modified')
                        };
                        break;
                    }
                }
                if (clinic)
                {
                    var interval = clinic.waitTime;
                    if (!(isNaN(parseInt(interval)) && interval == "Disabled")) {
                        var waitlegend = 'Urgent Care Wait Time';
                        var d = clinic.modified;
                        var dateElement = (d.getMonth() + 1) + '/' + d.getDate() + '/' + d.getFullYear();
                        var timeElement = (d.getHours() % 12 === 0 ? '12' : d.getHours() % 12).toString() + ':' + (d.getMinutes() < 10 ? '0' : '') + d.getMinutes() + ' ' + (d.getHours() < 12 ? 'AM' : 'PM'); 
                        var waitmodify = 'Last Updated ' + dateElement + ' ' + timeElement;
                        var intervalString;
                        if (isNaN(parseInt(interval))) {
                            intervalString = interval;
                        }
                        else {
                            if (interval == 0) {
                                intervalString = 'No Wait';
                            }
                            else {
                                var hours = Math.floor(interval / 60);
                                var minutes = interval % 60;
                                var hourstring = hours == 0 ? '' : hours == 1 ? '1 hr' : hours.toString() + ' hrs';
                                var minutesstring = minutes == 0 ? '' : (' ' + (minutes < 10 ? '0' : '') + minutes.toString() + ' mins');
                                intervalString = hourstring + minutesstring;
                            }
                        }
                        var listItemInfo = '<span id="waitlegend"  class="wait-legend">' + waitlegend + '</span>';
                        listItemInfo += '<span id="waittime"  class="wait-waittime">' + intervalString + '</span>';
                        listItemInfo += '<span id="waitmodify"  class="wait-modify">' + waitmodify + '</span>';
                        $(".wait-box").html(listItemInfo);
                    }
                }
            }
            function onQueryFailed(sender, args) {
            }
            var clientContext = new SP.ClientContext.get_current();
            var website = clientContext.get_site();
            var rootWeb = website.get_rootWeb();
            var lists = rootWeb.get_lists();
            var list = lists.getByTitle("Clinic Wait Times");
            var items = list.getItems(new SP.CamlQuery());
            clientContext.load(items);
            clientContext.executeQueryAsync(Function.createDelegate(this, onQuerySucceeded), Function.createDelegate(this, onQueryFailed));
        }

        if ($(".wait-box").length) {
            // Make sure the SharePoint script file 'sp.js' is loaded before your code runs.
            SP.SOD.executeFunc('sp.js', 'SP.ClientContext', waitClinic);
        }
        if ($(".clinic-summary").length) {
            SP.SOD.executeFunc('sp.js', 'SP.ClientContext',waitTimes);
        }
    });
}(jQuery));
