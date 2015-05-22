// -------------------------------------------------------------------
// Avocado Panel Custom JS/jQuery
// -------------------------------------------------------------------

// jQuery Waits for Document to Load
// -------------------------------------------------------------------
// URL: http://jquery.com
// -------------------------------------------------------------------

jQuery(document).ready(function() {


// Typhead (bootstrap)
// -------------------------------------------------------------------
// URL: http://twitter.github.io/bootstrap/javascript.html#typeahead
// -------------------------------------------------------------------


if ($(".typeahead")[0]){

    $('.typeahead').typeahead({
        source: [ "Afghanistan", "Albania", "Algeria", "Andorra", "Angola", "Antigua and Barbuda", 
                  "Argentina", "Armenia", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", 
                  "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bhutan", "Bolivia", 
                  "Bosnia and Herzegovina", "Botswana", "Brazil", "Brunei", "Bulgaria", "Burkina Faso", "Burundi", 
                  "Cambodia", "Cameroon", "Canada", "Cape Verde", "Central African Republic", "Chad", "Chile", "China", 
                  "Colombi", "Comoros", "Congo (Brazzaville)", "Congo", "Costa Rica", "Cote d'Ivoire", "Croatia", "Cuba", 
                  "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "East Timor (Timor Timur)", 
                  "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Fiji", "Finland", "France", 
                  "Gabon", "Gambia, The", "Georgia", "Germany", "Ghana", "Greece", "Grenada", "Guatemala", "Guinea", "Guinea-Bissau", 
                  "Guyana", "Haiti", "Honduras", "Hungary", "Iceland", "India", "Indonesia", "Iran", "Iraq", "Ireland", "Israel", 
                  "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, North", "Korea, South", 
                  "Kuwait", "Kyrgyzstan", "Laos", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libya", "Liechtenstein", 
                  "Lithuania", "Luxembourg", "Macedonia", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", 
                  "Marshall Islands", "Mauritania", "Mauritius", "Mexico", "Micronesia", "Moldova", "Monaco", "Mongolia", 
                  "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "New Zealand", 
                  "Nicaragua", "Niger", "Nigeria", "Norway", "Oman", "Pakistan", "Palau", "Panama", "Papua New Guinea", "Paraguay", 
                  "Peru", "Philippines", "Poland", "Portugal", "Qatar", "Romania", "Russia", "Rwanda", "Saint Kitts and Nevis", 
                  "Saint Lucia", "Saint Vincent", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", 
                  "Serbia and Montenegro", "Seychelles", "Sierra Leone", "Singapore", "Slovakia", "Slovenia", "Solomon Islands", 
                  "Somalia", "South Africa", "Spain", "Sri Lanka", "Sudan", "Suriname", "Swaziland", "Sweden", "Switzerland", 
                  "Syria", "Taiwan", "Tajikistan", "Tanzania", "Thailand", "Togo", "Tonga", "Trinidad and Tobago", "Tunisia", 
                  "Turkey", "Turkmenistan", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", 
                  "Uruguay", "Uzbekistan", "Vanuatu", "Vatican City", "Venezuela", "Vietnam", "Yemen", "Zambia", "Zimbabwe" 
                  ]
        }
    );

};

// Pajinate Pagination
// -------------------------------------------------------------------
// URL: https://github.com/wesnolte/Pajinate/
// -------------------------------------------------------------------

if ($("#pagination-activity")[0]){
  $('#pagination-activity').pajinate({
     items_per_page : 9,
    });
};

if ($("#pagination-messages")[0]){
  $('#pagination-messages').pajinate({
    items_per_page : 6,
  });
};

if ($("#pagination-todo")[0]){

  function todoPagination() {
    $('#pagination-todo').pajinate({
      items_per_page : 11,
    });
  }

  todoPagination();

};

// Theme Changer (Custom Made)
// -------------------------------------------------------------------
// URL: www.lycheedesigns.com
// -------------------------------------------------------------------

$("#theme a").hover(
   function() {
       // Called when the mouse enters the element
       var style = $(this).attr("data-style");
        $('#theme-style').attr("href", "assets/css/theme/" + style + ".css");
   },
   function() {
        var style = $("#theme a.active").attr("data-style");
        $('#theme-style').attr("href", "assets/css/theme/" + style + ".css");
   }
);

$('#theme a').click(function () {
    var style = $(this).attr("data-style");
    $('#theme-style').attr("href", "assets/css/theme/" + style + ".css");

    $.cookie("style", style);

    $("#theme a").attr("class", "");
    $(this).attr("class", "active");

});


// Photoswipe
// -------------------------------------------------------------------
// URL: http://www.photoswipe.com/
// -------------------------------------------------------------------

if ($(".gallery")[0]){

    function photoSwiper() {
        var myPhotoSwipe = $(".gallery a").photoSwipe(
            { 
                enableMouseWheel: true, 
                enableKeyboard: true,
                zIndex: 2034 
            });
    };

    photoSwiper();
};

// Gallery Hover
// -------------------------------------------------------------------
// URL: www.lycheedesigns.com
// -------------------------------------------------------------------

if ($(".gallery")[0]){

    function galleryHover() {
        $(".gallery a").hover(
            function() {
                // Called when the mouse enters the element
                $(this).prepend("<div class='icon-eye-open'></div>");

                $(this).find("img").css({
                    "filter": "alpha(opacity=100)",
                    "opacity": "1",
                  });
            },
            function() {
                // Called when the mouse leaves the element
                $(this).find(".icon-eye-open").remove();

                $(this).find("img").css({
                    "filter": "alpha(opacity=75)",
                    "opacity": "0.75",
                  });
            }
         );
    };
    galleryHover();
};

// Isotope
// -------------------------------------------------------------------
// URL: http://isotope.metafizzy.co/
// -------------------------------------------------------------------

if ($(".gallery")[0]){

  $(function () {

      var $container = $('.gallery');

      $container.isotope({
          itemSelector: '.element',
          layoutMode: 'masonry',
      });


  $(window).smartresize(function(){
     $container.isotope({
       resizable: false, // disable normal resizing
       masonry: { columnWidth: $container.width() / 5 }
     });
     // trigger resize to trigger isotope
  }).smartresize();

  // trigger isotope again after images have loaded
  $container.imagesLoaded( function(){
    $(window).smartresize();
  });


      var $optionSets = $('.gallery-sorting'),
          $optionLinks = $optionSets.find('a');

      $optionLinks.click(function () {
          var $this = $(this);
          // don't proceed if already selected
          if ($this.parent().hasClass('active')) {
              return false;
          }
          var $optionSet = $this.parents();

          $optionSet.find('.gallery-sorting .active').removeClass('active');
          $this.parent().addClass('active');

          // make option object dynamically, i.e. { filter: '.my-filter-class' }
          var options = {},
          value = $this.attr('data-option-value');

          // parse 'false' as false boolean
          console.log(value);
          options = {filter: value};

          // creativewise, apply new options
          $container.isotope(options);

          return false;
      });


  });

};


// DataTables
// -------------------------------------------------------------------
// URL: http://www.datatables.net/
// -------------------------------------------------------------------

if ($(".data-table")[0]){

    $('.data-table').dataTable({ordering: false, "bSort": false});

};

// Tabs (bootstrap)
// -------------------------------------------------------------------
// URL: http://twitter.github.io/bootstrap/javascript.html#tabs
// -------------------------------------------------------------------

    $('.tab-container a').click(function (e) {
      e.preventDefault();
      $(this).tab('show');
    })

// Todo List
// -------------------------------------------------------------------
// URL: www.lycheedesigns.com
// -------------------------------------------------------------------

if ($(".todo")[0]){

function todoItemDone() {

    // Strikethrough item if is checkbox is clicked
    $('.todo .checkbox input').unbind('click');

    $('.todo .checkbox input').click( function() {
      console.log($(this));

      $(this).parent().toggleClass('checked');
      $(this).parent().parent().toggleClass('todo-done');

    });

};

$('.todo .todo-add').keypress(function (e) {

    // If enter is pressed, adds item
    if (e.which == 13) {

      var todoDescription = $(this).val();

      $(this).parent().parent().find(".pagination-content").prepend(
        '<div class="item todo-new">'
          + '<button type="button" class="close" data-dismiss="alert">&times;</button>'
          + '<label class="checkbox">'
              + '<input type="checkbox"> ' 
              + '<i class="icon-asterisk"></i>'
              + todoDescription
          + '</label>'
      + '</div>');

      todoItemDone();
      todoPagination();

      return false;
    }

});

todoItemDone();

};

// WYSIWYG (bootstrap)
// -------------------------------------------------------------------
// URL: http://mindmup.github.io/bootstrap-wysiwyg/
// -------------------------------------------------------------------

function initToolbarBootstrapBindings() {
      var fonts = ['Serif', 'Sans', 'Arial', 
            'Courier New', 'Comic Sans MS', 'Helvetica',
            'Lucida Grande', 'Lucida Sans', 'Tahoma', 'Times',
            'Times New Roman', 'Verdana'],
            fontTarget = $('[title=Font]').siblings('.dropdown-menu');
      $.each(fonts, function (idx, fontName) {
          fontTarget.append($('<li><a data-edit="fontName ' + fontName + '" style="font-family:\''+ fontName +'\'">'+fontName + '</a></li>'));
      });
      $('a[title]').tooltip({container:'body'});
        $('.dropdown-menu input').click(function() {return false;})
            .change(function () {$(this).parent('.dropdown-menu').siblings('.dropdown-toggle').dropdown('toggle');})
        .keydown('esc', function () {this.value='';$(this).change();});

      $('[data-role=magic-overlay]').each(function () { 
        var overlay = $(this), target = $(overlay.data('target')); 
        overlay.css('opacity', 0).css('position', 'absolute').offset(target.offset()).width(target.outerWidth()).height(target.outerHeight());
      });
    };
    initToolbarBootstrapBindings();  

$('#textarea').wysiwyg();
$('#sendmessage').wysiwyg();

// Google Maps API 
// -------------------------------------------------------------------
// URL: https://developers.google.com/maps/documentation/javascript/
// -------------------------------------------------------------------

if ($("#map-example1,#map-example2,#map-example3,#map-example4,#map-example5")[0]){

    var example1;
    var example2;
    var example3;
    var example4;
    var example5;

    function initialize() {

        // Basic Map
        var exampleOneOptions = {
            zoom: 3,
            center: new google.maps.LatLng(48.30, 23.23),
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };

        example1 = new google.maps.Map(document.getElementById('map-example1'),
          exampleOneOptions);


        // Hybrid Map
        var exampleTwoOptions = {
            zoom: 2,
            center: new google.maps.LatLng(24.32, 50.644),
            mapTypeId: google.maps.MapTypeId.HYBRID
        };

        example2 = new google.maps.Map(document.getElementById('map-example2'),
          exampleTwoOptions);


        // Terrain Map
        var exampleThreeOptions = {
            zoom: 8,
            center: new google.maps.LatLng(44.397, 23.644),
            mapTypeId: google.maps.MapTypeId.TERRAIN
        };

        example3 = new google.maps.Map(document.getElementById('map-example3'),
          exampleThreeOptions);


        // Satelite Map
        var exampleFourOptions = {
            zoom: 3,
            center: new google.maps.LatLng(48.30, 23.23),
            mapTypeId: google.maps.MapTypeId.SATELLITE
        };

        example4 = new google.maps.Map(document.getElementById('map-example4'),
          exampleFourOptions);


        // Satelite Map
        var exampleFiveOptions = {
            zoom: 1,
            center: new google.maps.LatLng(-18.397,53.644),
            mapTypeId: google.maps.MapTypeId.SATELLITE
        };

        example5 = new google.maps.Map(document.getElementById('map-example5'),
          exampleFiveOptions);
    }

    google.maps.event.addDomListener(window, 'load', initialize);

};

// jQuery UI: Sortable for Box Widgets (wells)
// -------------------------------------------------------------------
// URL: http://jqueryui.com 
// -------------------------------------------------------------------

/*
  $(function() {
    $(".row-fluid").sortable({
        connectWith: ".row-fluid",
        start: function(e, ui){
          ui.placeholder.height(ui.item.height());
        }
    });
 
 */
    $(".container").find(".top-bar")
        .append("<div>"
                + "<a href='#' class='top-bar-minimize' rel='tooltip' title='Minimize'><i class='icon-resize-small'></i></a> "
                + "</div>")
        .end()
    /*.find(".well");*/

    $(".top-bar div .top-bar-minimize").click(function() {
          $this = $(this).parent().parent().parent();
        if($($this).find(".top-bar").hasClass("top-bar-closed")) {
            $($this).find(".top-bar").removeClass("top-bar-closed");
            $($this).find(".well").slideDown({duration: 1000, easing: 'easeOutBack'});
        }
        else {
            $($this).find(".well").slideUp('medium', function() {
                $(this).parent().find(".top-bar").addClass("top-bar-closed"); 
            });
        };
        
        return false;
    });
/*
    $(".row-fluid").disableSelection();

  });
*/




// Flot Example
// -------------------------------------------------------------------
// URL: http://www.flotcharts.org
// -------------------------------------------------------------------

if ($(".chart1")[0]){

    // Example Data
    var d1 = [[1262304000000, 123], [1264982400000, 453], [1267401600000, 3894], [1270080000000, 9542], [1272672000000, 8323], [1275350400000, 9343], [1277942400000, 9232], [1280620800000, 8343], [1283299200000, 7343], [1285891200000, 3343], [1288569600000, 2322], [1291161600000, 2012]];
 	var d2 = [[1262304000000, 6], [1264982400000, 60], [1267401600000, 2043], [1270080000000, 2198], [1272672000000, 2660], [1275350400000, 2782], [1277942400000, 2430], [1280620800000, 2427], [1283299200000, 2100], [1285891200000, 1214], [1288569600000, 1057], [1291161600000, 1025]];
    
    // Chart Finder
    $.plot($(".chart1"),
           [ 
           	{ 
           		label: "Hits", 
	            data: d1, 
	            color: '#8dbdca',
	            shadowSize: 0
           	}, 
           	{ 
           		label: "Unique Hits", 
	            data: d2, 
	            color: '#eb8460',
	            shadowSize: 0
           	} 
           ], {
        xaxis: {
            show: true,
            min: (new Date(2009, 12, 1)).getTime(),
            max: (new Date(2010, 11, 2)).getTime(),
            mode: "time",
            tickSize: [1, "month"],
            monthNames: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
            tickLength: 1,
            axisLabel: 'Month',
            axisLabelFontSizePixels: 11
        },
        yaxis: {
            axisLabel: 'Traffic',
            axisLabelUseCanvas: true,
            axisLabelFontSizePixels: 11,
            autoscaleMargin: 0.01,
            axisLabelPadding: 5,
            tickColor: "#d8d8d8"
        },
        series: {
            lines: {
                show: true, 
                fill: true,
                fillColor: { colors: [ { opacity: 0.04 }, { opacity: 0.18 } ] },
                lineWidth: 2
            },
            points: {
                show: true,
                radius: 3,
                fill: true,
                fillColor: "#f0f0f0",
                symbol: "circle",
                lineWidth: 2
            }
        },
       	grid: { 
       		hoverable: true, 
       		clickable: true, 
       		borderWidth: 0, 
       		color: "#939393",
       		labelMargin: 20
       	},
        legend: {
            show: false
        }
    });

    function showTooltip(x, y, contents) {
        $('<div class="tooltip bottom"><div class="tooltip-arrow"></div><div class="tooltip-inner">' + contents + '</div></div>').css( {
            top: y + 10,
            left: x - 58,
            'z-index': '9999',
            opacity: 0.9,
        }).appendTo("body").fadeIn(200);
    }

    var previousPoint = null;
    $(".chart1").bind("plothover", function (event, pos, item) {
        $("#x").text(pos.x.toFixed(0));
        $("#y").text(pos.y.toFixed(0));

        if ($(".chart1").length > 0) {
            if (item) {
                if (previousPoint != item.dataIndex) {
                    previousPoint = item.dataIndex;
                    
                    $(".tooltip").remove();
                    var x = item.datapoint[0].toFixed(0),
                        y = item.datapoint[1].toFixed(0);
                    
                    showTooltip(item.pageX, item.pageY,
                                "<strong>" + y + "</strong> " + item.series.label);
                }
            }
            else {
                $(".tooltip").remove();
                previousPoint = null;            
            }
        }
    });

    $(".chart1").bind("plotclick", function (event, pos, item) {
        if (item) {
            $("#clickdata").text("You clicked point " + item.dataIndex + " in " + item.series.label + ".");
            plot.highlight(item.series, item.datapoint);
        }
    });
};


// Pie
if ($(".pie1")[0]){

    //some data
    var d1 = [];
    for (var i = 0; i <= 10; i += 1)
        d1.push([i, parseInt(Math.random() * 30)]);
    
    var d2 = [];
    for (var i = 0; i <= 10; i += 1)
        d2.push([i, parseInt(Math.random() * 30)]);
    
    var d3 = [];
    for (var i = 0; i <= 10; i += 1)
        d3.push([i, parseInt(Math.random() * 30)]);
    
    var ds = new Array();
    
    ds.push({
        label: "Expenses",
        data:d1,
        bars: {order: 1}
    });
    ds.push({
        label: "Sales",
        data:d2,
        bars: {order: 2}
    });
    ds.push({
        label: "Bonuses",
        data:d3,
        bars: {order: 3}
    });
    this.data = ds;

    jQuery.plot(jQuery(".pie1"), this.data, {
            colors: ['#88bbc8','#eb815c','#7fc18d','#cea0db','#bbd99b'],   
            legend: {
                backgroundColor: "rgba(0,0,0,0)"
            },  
            height: "100%",
            width: "100%",    
            series: {
                pie: { 
                    show: true,
                    innerRadius: 0.5,
                    stroke: {
                        color: "#f0f0f0",
                        width: 0.0
                    }
                }
            }
    });
};

// Pie Chart 2
if ($(".pie2")[0]){

    //some data
    var d1 = [];
    for (var i = 0; i <= 10; i += 1)
        d1.push([i, parseInt(Math.random() * 30)]);
    
    var d2 = [];
    for (var i = 0; i <= 10; i += 1)
        d2.push([i, parseInt(Math.random() * 30)]);
    
    var d3 = [];
    for (var i = 0; i <= 10; i += 1)
        d3.push([i, parseInt(Math.random() * 30)]);
    
    var ds = new Array();
    
    ds.push({
        label: "Expenses",
        data:d1,
        bars: {order: 1}
    });
    ds.push({
        label: "Sales",
        data:d2,
        bars: {order: 2}
    });
    ds.push({
        label: "Bonuses",
        data:d3,
        bars: {order: 3}
    });
    this.data = ds;

    jQuery.plot(jQuery(".pie2"), this.data, {
            colors: ['#88bbc8','#eb815c','#7fc18d','#cea0db','#bbd99b'],  

            series: {
            pie: { 
                show: true,
                radius: 1,
                label: {
                    show: true,
                    radius: 2/3,
                    formatter: function(label, series){
                        return '<div style="font-size:8pt;text-align:center;padding:2px;color:white;">'+label+'<br/>'+Math.round(series.percent)+'%</div>';
                    },
                    threshold: 0.1
                }
            }
        },
        legend: {
            show: false
        }
            
    });

};

// Realtime Chart
if ($(".realtimechart")[0]){

    $(function () {
        // we use an inline data source in the example, usually data would
        // be fetched from a server
        var data = [], totalPoints = 300;
        function getRandomData() {
            if (data.length > 0)
                data = data.slice(1);

            // do a random walk
            while (data.length < totalPoints) {
                var prev = data.length > 0 ? data[data.length - 1] : 50;
                var y = prev + Math.random() * 10 - 5;
                if (y < 0)
                    y = 0;
                if (y > 100)
                    y = 100;
                data.push(y);
            }

            // zip the generated y values with the x values
            var res = [];
            for (var i = 0; i < data.length; ++i)
                res.push([i, data[i]])
            return res;
        }

        // graph interval
        var updateInterval = 30;

        // setup plot
        var options = {
            colors: ['#40c67f'],  
            grid: { 
                borderWidth: 0, 
                color: "#939393"
            },
            series: {
            lines: {
                show: true, 
                fill: true,
                fillColor: { colors: [ { opacity: 0.04 }, { opacity: 0.18 } ] },
                lineWidth: 2
            },shadowSize: 0 }, // drawing is faster without shadows
            yaxis: { min: 0, max: 100 },
            xaxis: { show: false }
        };
        var plot = $.plot($(".realtimechart"), [ getRandomData() ], options);

        function update() {
            plot.setData([ getRandomData() ]);
            // since the axes don't change, we don't need to call plot.setupGrid()
            plot.draw();
            
            setTimeout(update, updateInterval);
        }

        update();
    });
};


// Bars
if ($(".bars1")[0]){

$(function() {

    //some data
    var d1 = [];
    for (var i = 0; i <= 10; i += 1)
        d1.push([i, parseInt(Math.random() * 30)]);
    
    var d2 = [];
    for (var i = 0; i <= 10; i += 1)
        d2.push([i, parseInt(Math.random() * 30)]);
    
    var d3 = [];
    for (var i = 0; i <= 10; i += 1)
        d3.push([i, parseInt(Math.random() * 30)]);
    
    var ds = new Array();
    
    ds.push({
        label: "Expenses",
        data:d1,
        bars: {order: 1}
    });
    ds.push({
        label: "Sales",
        data:d2,
        bars: {order: 2}
    });
    ds.push({
        label: "Bonuses",
        data:d3,
        bars: {order: 3}
    });
    this.data = ds;

    $.plot(".bars1", this.data, {
        colors: ['#88bbc8','#eb815c','#7fc18d','#cea0db','#bbd99b'],   
        grid: { 
            borderWidth: 0, 
            color: "#939393"
        },
        legend: {
            margin: 0,
            noColumns: 3
            },  
        series: {
            bars: {
                show: true,
                barWidth: 0.15,
                align: "center",
                fillColor: { colors: [ { opacity: 1 }, { opacity: 1 } ] },
            }
        },
        xaxis: {
            tickLength: 1,
            mode: "categories",
            ticks: [[0,'Jan'],[1,'Feb'],[2,'Mar'],[3,'Apr'],[4,'May'],[5,'Jun'],[6,'Jul'],[7,'Aug'],[8,'Sep'],[9,'Oct'],[10,'Nov'],[11,'Dec']]
        }
    });


});

};



// Calendar
// -------------------------------------------------------------------
// URL: http://bootstrap.twitter.com
// -------------------------------------------------------------------

// Standard Calendar
var date = new Date();
var d = date.getDate();
var m = date.getMonth();
var y = date.getFullYear();

$('#calendar').fullCalendar({
    editable: true,
    events: [
        {
            title: 'All Day Event',
            start: new Date(y, m, 1)
        },
        {
            title: 'Long Event',
            start: new Date(y, m, d-5),
            end: new Date(y, m, d-2)
        },
        {
            id: 999,
            title: 'Repeating Event',
            start: new Date(y, m, d-3, 16, 0),
            allDay: false
        },
        {
            id: 999,
            title: 'Repeating Event',
            start: new Date(y, m, d+4, 16, 0),
            allDay: false
        },
        {
            title: 'Meeting',
            start: new Date(y, m, d, 10, 30),
            allDay: false
        },
        {
            title: 'Lunch',
            start: new Date(y, m, d, 12, 0),
            end: new Date(y, m, d, 14, 0),
            allDay: false
        },
        {
            title: 'Birthday Party',
            start: new Date(y, m, d+1, 19, 0),
            end: new Date(y, m, d+1, 22, 30),
            allDay: false
        },
        {
            title: 'Click for Google',
            start: new Date(y, m, 28),
            end: new Date(y, m, 29),
            url: 'http://google.com/'
        }
    ]
});

// Agenda Calendar

 var date = new Date();
 var d = date.getDate();
 var m = date.getMonth();
 var y = date.getFullYear();
 
 $('#calendar-agenda').fullCalendar({
     header: {
         left: 'prev,next today',
         center: 'title',
         right: 'month,agendaWeek,agendaDay'
     },
     editable: true,
     events: [
         {
             title: 'All Day Event',
             start: new Date(y, m, 1)
         },
         {
             title: 'Long Event',
             start: new Date(y, m, d-5),
             end: new Date(y, m, d-2)
         },
         {
             id: 999,
             title: 'Repeating Event',
             start: new Date(y, m, d-3, 16, 0),
             allDay: false
         },
         {
             id: 999,
             title: 'Repeating Event',
             start: new Date(y, m, d+4, 16, 0),
             allDay: false
         },
         {
             title: 'Meeting',
             start: new Date(y, m, d, 10, 30),
             allDay: false
         },
         {
             title: 'Lunch',
             start: new Date(y, m, d, 12, 0),
             end: new Date(y, m, d, 14, 0),
             allDay: false
         },
         {
             title: 'Birthday Party',
             start: new Date(y, m, d+1, 19, 0),
             end: new Date(y, m, d+1, 22, 30),
             allDay: false
         },
         {
             title: 'Click for Google',
             start: new Date(y, m, 28),
             end: new Date(y, m, 29),
             url: 'http://google.com/'
         }
     ]
 });

// Selectable Calendar

var date = new Date();
var d = date.getDate();
var m = date.getMonth();
var y = date.getFullYear();

var calendar = $('#calendar-selectable').fullCalendar({
    header: {
        left: 'prev,next today',
        center: 'title',
        right: 'month,agendaWeek,agendaDay'
    },
    selectable: true,
    selectHelper: true,
    select: function(start, end, allDay) {
        var title = prompt('Event Title:');
        if (title) {
            calendar.fullCalendar('renderEvent',
                {
                    title: title,
                    start: start,
                    end: end,
                    allDay: allDay
                },
                true // make the event "stick"
            );
        }
        calendar.fullCalendar('unselect');
    },
    editable: true,
    events: [
        {
            title: 'All Day Event',
            start: new Date(y, m, 1)
        },
        {
            title: 'Long Event',
            start: new Date(y, m, d-5),
            end: new Date(y, m, d-2)
        },
        {
            id: 999,
            title: 'Repeating Event',
            start: new Date(y, m, d-3, 16, 0),
            allDay: false
        },
        {
            id: 999,
            title: 'Repeating Event',
            start: new Date(y, m, d+4, 16, 0),
            allDay: false
        },
        {
            title: 'Meeting',
            start: new Date(y, m, d, 10, 30),
            allDay: false
        },
        {
            title: 'Lunch',
            start: new Date(y, m, d, 12, 0),
            end: new Date(y, m, d, 14, 0),
            allDay: false
        },
        {
            title: 'Birthday Party',
            start: new Date(y, m, d+1, 19, 0),
            end: new Date(y, m, d+1, 22, 30),
            allDay: false
        },
        {
            title: 'Click for Google',
            start: new Date(y, m, 28),
            end: new Date(y, m, 29),
            url: 'http://google.com/'
        }
    ]
});

// External Dragable Calendar

/* initialize the external events
-----------------------------------------------------------------*/

$('#calendar-external-events .external-event').each(function() {

    // create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
    // it doesn't need to have a start or end
    var eventObject = {
        title: $.trim($(this).text()) // use the element's text as the event title
    };
    
    // store the Event Object in the DOM element so we can get to it later
    $(this).data('eventObject', eventObject);
    
    // make the event draggable using jQuery UI
    $(this).draggable({
        zIndex: 999,
        revert: true,      // will cause the event to go back to its
        revertDuration: 0  //  original position after the drag
    });
    
});


/* initialize the calendar
-----------------------------------------------------------------*/

$('#calendar-external').fullCalendar({
    header: {
        left: 'prev,next today',
        center: 'title',
        right: 'month,agendaWeek,agendaDay'
    },
    editable: true,
    droppable: true, // this allows things to be dropped onto the calendar !!!
    drop: function(date, allDay) { // this function is called when something is dropped
    
        // retrieve the dropped element's stored Event Object
        var originalEventObject = $(this).data('eventObject');
        
        // we need to copy it, so that multiple events don't have a reference to the same object
        var copiedEventObject = $.extend({}, originalEventObject);
        
        // assign it the date that was reported
        copiedEventObject.start = date;
        copiedEventObject.allDay = allDay;
        
        // render the event on the calendar
        // the last `true` argument determines if the event "sticks" (http://arshaw.com/fullcalendar/docs/event_rendering/renderEvent/)
        $('#calendar-external').fullCalendar('renderEvent', copiedEventObject, true);
        
        // is the "remove after drop" checkbox checked?
        if ($('#drop-remove').is(':checked')) {
            // if so, remove the element from the "Draggable Events" list
            $(this).remove();
        }
        
    }
});


// Tooltip (bootstrap)
// -------------------------------------------------------------------
// URL: http://bootstrap.twitter.com
// -------------------------------------------------------------------

$("[rel='tooltip']").tooltip();

$("[rel='tooltip']").each(function( index ) {
  $(this).data('tooltip').options.placement = 'bottom';
});

// Chosen
// -------------------------------------------------------------------
// URL: http://harvesthq.github.io/chosen/
// -------------------------------------------------------------------

if ($("select")[0]){

  $("select").chosen({disable_search_threshold: 10});

};


// END: jQuery Waits for Document to Load
// -------------------------------------------------------------------
// URL: http://jquery.com
// -------------------------------------------------------------------

});

