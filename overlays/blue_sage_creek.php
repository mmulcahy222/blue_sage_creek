<script>
var map;
var thisInfo = [];
var thisLot = [];
var cwindow;
var overlay; 
//NOTE, THIS WILL ONLY WORK WHEN THE PHP FILE FEEDS THAT VARIABLE BELOW
var data_location = '<?php echo isset($xml_data_path) ? $xml_data_path : 'blue_sage_creek_data.xml'; ?>';
var srcImage = '<?php echo isset($src_image) ? $src_image : 'images/blue_sage_creek_full.jpg'; ?>';

function get_google_maps_element()
{
  google_maps_element = document.querySelectorAll(".qodef-google-map")[0]
  if(google_maps_element == undefined)
  {
    google_maps_element = document.getElementById("map_canvas");
  }
  return google_maps_element;
}

image_north = 41.22662787024184;
image_south = 41.21937536164211;
image_east = -96.24405157752379;
image_west = -96.25483290384219;

function initialize_gmap() {
  var latlng = new google.maps.LatLng((image_north + image_south) / 2 , (image_east + image_west) / 2);
  var ltStyle = [
    {
    featureType: "poi",
    stylers: [
    { visibility: "off" }
    ]
    },
    {
    featureType: "landscape.natural",
    stylers: [
    { visibility: "on" }
    ]
    }
  ];

  var styledMapOptions = {
    name: "Lanoha"
  }

  var mapOptions = {
  zoom: 15,
  center: latlng,
  mapTypeControl: false  
  };
  var ltMapType = new google.maps.StyledMapType(ltStyle, styledMapOptions);
  var map = new google.maps.Map(get_google_maps_element(),mapOptions);

  var zoomLevel;
  google.maps.event.addListener(map, 'zoom_changed', function() {
  zoomLevel = map.getZoom();
  if (zoomLevel == 19) {
  console.log("=19", zoomLevel);
  var swBound = new google.maps.LatLng( image_south, image_west);
  var neBound = new google.maps.LatLng( image_north, image_east);
  var bounds = new google.maps.LatLngBounds(swBound, neBound);
  overlay.bounds_ = bounds;
  }
  else if (zoomLevel == 18) {
  console.log("=18", zoomLevel);
  var swBound = new google.maps.LatLng( image_south, image_west);
  var neBound = new google.maps.LatLng( image_north, image_east);
  var bounds = new google.maps.LatLngBounds(swBound, neBound);
  overlay.bounds_ = bounds;
  }
  else if (zoomLevel == 17) {
  console.log("=17", zoomLevel);
  var swBound = new google.maps.LatLng( image_south, image_west);
  var neBound = new google.maps.LatLng( image_north, image_east);
  var bounds = new google.maps.LatLngBounds(swBound, neBound);
  overlay.bounds_ = bounds;
  }
  else if (zoomLevel == 16) {
  console.log("=16", zoomLevel);
  var swBound = new google.maps.LatLng( image_south, image_west);
  var neBound = new google.maps.LatLng( image_north, image_east);
  var bounds = new google.maps.LatLngBounds(swBound, neBound);
  overlay.bounds_ = bounds;
  }
  else if (zoomLevel == 15) {
  console.log("=15", zoomLevel);
  var swBound = new google.maps.LatLng( image_south, image_west);
  var neBound = new google.maps.LatLng( image_north, image_east);
  var bounds = new google.maps.LatLngBounds(swBound, neBound);
  overlay.bounds_ = bounds;
  }
  else if (zoomLevel < 15) {
  console.log("< 15", zoomLevel);
  var swBound = new google.maps.LatLng( image_south, image_west);
  var neBound = new google.maps.LatLng( image_north, image_east);
  var bounds = new google.maps.LatLngBounds(swBound, neBound);
  overlay.bounds_ = bounds;
  }
  else {
  console.log("else", zoomLevel);
  var swBound = new google.maps.LatLng( image_south, image_west);
  var neBound = new google.maps.LatLng( image_north, image_east);
  var bounds = new google.maps.LatLngBounds(swBound, neBound);
  overlay.bounds_ = bounds;
  }
  });

  map.mapTypes.set('Lanoha', ltMapType);
  map.setMapTypeId('Lanoha');
  var swBound = new google.maps.LatLng( image_south, image_west);
  var neBound = new google.maps.LatLng( image_north, image_east);
  var bounds = new google.maps.LatLngBounds(swBound, neBound);


  overlay = new USGSOverlay(bounds, srcImage, map);
  cwindow = new google.maps.InfoWindow();

  jQuery.ajax({
  //url: "http://lanohadevelopment.com/wp-content/themes/lanoha/temp_prairies.xml",
  url: data_location,
  dataType: "xml",
  success: function(xml) {
  jQuery(xml).find('lots').each(function(){
  jQuery(xml).find('lot').each(function(){
  var lotid = jQuery(this).attr("id");
  var name = jQuery(this).attr("name");
  var status = jQuery(this).attr("status");
  var statusbg;
  switch (status) {
    case '1':
    statusbg = "#d2a02c";
    break;
    case '2':
    statusbg = "#26904f";
    break;
    case '3':
    statusbg = "#840216";
    break;
  }
  var info = jQuery(this).find('infotext').text();
  var center = new google.maps.LatLng(parseFloat(jQuery(this).attr("clat")), parseFloat(jQuery(this).attr("clon")));
  var thepts = [];
  jQuery(this).find('point').each(function(){
  thepts.push(new google.maps.LatLng(parseFloat(jQuery(this).attr("lat")), parseFloat(jQuery(this).attr("lon"))));
  });
  thisLot = new google.maps.Polygon({
  map: map,
  clickable: true,
  paths: thepts,
  strokeColor: "#000000",
  strokeOpacity: 0.8,
  strokeWeight: .5,
  fillColor: statusbg,
  fillOpacity: .75 });
  google.maps.event.addListener(thisLot, 'click', function() {
  if (cwindow) {
  cwindow.close();
  }
  cwindow.setPosition(center);
  cwindow.setContent("<div style='width: 300px;'>" + info + "</div>");
  cwindow.open(map);
  });
  thisLot.setMap(map);
  });
  });
  }
  });
}

function USGSOverlay(bounds, image, map) {

  // Now initialize all properties.
  this.bounds_ = bounds;
  this.image_ = image;
  this.map_ = map;

  // We define a property to hold the image's
  // div. We'll actually create this div
  // upon receipt of the add() method so we'll
  // leave it null for now.
  this.div_ = null;

  // Explicitly call setMap() on this overlay
  this.setMap(map);
}

USGSOverlay.prototype = new google.maps.OverlayView();

USGSOverlay.prototype.draw = function() {

  // Size and position the overlay. We use a southwest and northeast
  // position of the overlay to peg it to the correct position and size.
  // We need to retrieve the projection from this overlay to do this.
  var overlayProjection = this.getProjection();

  // Retrieve the southwest and northeast coordinates of this overlay
  // in latlngs and convert them to pixels coordinates.
  // We'll use these coordinates to resize the DIV.
  var sw = overlayProjection.fromLatLngToDivPixel(this.bounds_.getSouthWest());
  var ne = overlayProjection.fromLatLngToDivPixel(this.bounds_.getNorthEast());

  // Resize the image's DIV to fit the indicated dimensions.
  var div = this.div_;
  div.style.left = sw.x + 'px';
  div.style.top = ne.y + 'px';
  div.style.width = (ne.x - sw.x) + 'px';
  div.style.height = (sw.y - ne.y) + 'px';
}

USGSOverlay.prototype.onAdd = function() {

  // Note: an overlay's receipt of onAdd() indicates that
  // the map's panes are now available for attaching
  // the overlay to the map via the DOM.

  // Create the DIV and set some basic attributes.
  var div = document.createElement('DIV');
  div.style.border = "none";
  div.style.borderWidth = "0px";
  div.style.position = "absolute";

  // Create an IMG element and attach it to the DIV.
  var img = document.createElement("img");
  img.src = this.image_;
  img.style.width = "100%";
  img.style.height = "100%";
  div.appendChild(img);

  // Set the overlay's div_ property to this DIV
  this.div_ = div;

  // We add an overlay to a map via one of the map's panes.
  // We'll add this overlay to the overlayImage pane.
  var panes = this.getPanes();
  panes.overlayLayer.appendChild(div);
}

jQuery(document).ready(function($){
  initialize_gmap();
});
</script>