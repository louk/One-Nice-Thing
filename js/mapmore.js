var EarthRadiusMeters = 6378137.0;
google.maps.LatLng.prototype.DestinationPoint = function (brng, dist) {
    var R = EarthRadiusMeters; 
    var brng = brng.toRad();
    var lat1 = this.lat().toRad(), lon1 = this.lng().toRad();
    var lat2 = Math.asin( Math.sin(lat1)*Math.cos(dist/R) + 
            Math.cos(lat1)*Math.sin(dist/R)*Math.cos(brng) );
    var lon2 = lon1 + Math.atan2(Math.sin(brng)*Math.sin(dist/R)*Math.cos(lat1), 
            Math.cos(dist/R)-Math.sin(lat1)*Math.sin(lat2));

    return new google.maps.LatLng(lat2.toDeg(), lon2.toDeg());
}
google.maps.LatLng.prototype.rhumbDestinationPoint = function (brng, dist) {
    var R = EarthRadiusMeters;
    var d = parseFloat(dist) / R;
    var lat1 = this.lat().toRad(), lon1 = this.lng().toRad();
    brng = brng.toRad();

    var lat2 = lat1 + d * Math.cos(brng);
    var dLat = lat2 - lat1;
    var dPhi = Math.log(Math.tan(lat2 / 2 + Math.PI / 4) / Math.tan(lat1 / 2 + Math.PI / 4));
    var q = (Math.abs(dLat) > 1e-10) ? dLat / dPhi : Math.cos(lat1);
    var dLon = d * Math.sin(brng) / q;
    if (Math.abs(lat2) > Math.PI / 2) {
        lat2 = lat2 > 0 ? Math.PI - lat2 : - (Math.PI - lat2);
    }
    var lon2 = (lon1 + dLon + Math.PI) % (2 * Math.PI) - Math.PI;

    if (isNaN(lat2) || isNaN(lon2)) {
        alert("bad Rhumb Destination Point");
        return null;
    }
    return new google.maps.LatLng(lat2.toDeg(), lon2.toDeg());
};
google.maps.LatLng.prototype.rhumbDistanceTo = function(point) { 
    var R = EarthRadiusMeters; 
    var lat1 = this.lat().toRad(), lat2 = point.lat().toRad(); 
    var dLat = (point.lat()-this.lat()).toRad(); 
    var dLon = Math.abs(point.lng()-this.lng()).toRad(); 

    var dPhi = Math.log(Math.tan(lat2/2+Math.PI/4)/Math.tan(lat1/2+Math.PI/4)); 
    var q = (!isNaN(dLat/dPhi) && isFinite(dLat/dPhi)) ? dLat/dPhi : Math.cos(lat1);
    if (dLon > Math.PI) dLon = 2*Math.PI - dLon; 
    var dist = Math.sqrt(dLat*dLat + q*q*dLon*dLon) * R;  

    return dist;
} 
Number.prototype.toRad = function () {
    return this * Math.PI / 180;
};
Number.prototype.toDeg = function () {
    return this * 180 / Math.PI;
};
Number.prototype.toBrng = function () {
    return (this.toDeg() + 360) % 360;
};

var infowindow = new google.maps.InfoWindow(
        { 
            size: new google.maps.Size(150,50)
        });

var map = null;
var bounds = null;
var gpolys = [];

// bool parameter child?
function createMarker(latlng, id, html, bool) {
    var contentString = html;
    if(bool){ 
        var marker = new google.maps.Marker({
            position: latlng,
            id: id,
            map: map,
            icon: "http://maps.google.com/mapfiles/ms/icons/green-dot.png",
            zIndex: Math.round(latlng.lat()*-100000)<<5
        });
    }else{ 
        var marker = new google.maps.Marker({
            position: latlng,
            id: id,
            map: map,
            zIndex: Math.round(latlng.lat()*-100000)<<5
        });
    }
    bounds.extend(latlng);
    google.maps.event.addListener(marker, 'click', function() {
        $('#loading').show();
        $.ajax({
            type: "POST",
            url: "index.php",
            data: {viewThings:marker.get("id")},
            success: function(data, textStatus, jqXHR)
        {

            for (var i = 0; i < data.length; i += 1) {
                bounds.extend(new google.maps.LatLng(data[i].lat, data[i].lng));
            }

            for (var i = 0; i < data.length; i += 1) {
                geodesic = new google.maps.Polyline({
                    path:[latlng, new google.maps.LatLng(data[i].lat, data[i].lng)],
                    strokeColor: "#40b553",
                    strokeOpacity: 0.8,
                    map: map,
                    geodesic:true,
                    strokeWeight: 4
                });
            }
            console.log(data.length);
            var content = "";
            for (var i = 0; i < data.length; i += 1) {
                content = '<div class="ui card" style="padding: 5px;text-align: left;"><div class="card"><div class="content"><img class="right floated mini ui image" src="'+data[i].avatar+'"><div class="header">'+data[i].name+'</div><div class="meta">'+data[i].location_name+'</div><div class="description">'+data[i].nice_thing+'</div></div></div><div class="short_Info"><a href="index.php?explore='+data[i].id+'">Learn more</a></div></div>';
                createMarker(new google.maps.LatLng(data[i].lat, data[i].lng), data[i].refered_user, content, true);
            }
            $('#loading').hide();
            infowindow.setContent(contentString); 
            infowindow.open(map,marker);
            map.fitBounds(bounds);
        }
        });
    });
}

function createClickablePoly(poly, html, label, point) {
    gpolys.push(poly);
    if (!point && poly.getPath 
            && poly.getPath().getLength 
            && (poly.getPath().getLength > 0) 
            && poly.getPath().getAt(0)) { point = poly.getPath().getAt(0); } 
    var poly_num = gpolys.length - 1;
    if (!html) {html = "";}
    else { html += "<br>";}
    var length = poly.Distance();
    if (length > 1000) {
        html += "length="+poly.Distance().toFixed(3)/1000+" km";
    } else {
        html += "length="+poly.Distance().toFixed(3)+" meters";
    }
    for (var i=0;i<poly.getPath().getLength();i++) {
        html += "<br>poly["+poly_num+"]["+i+"]="+poly.getPath().getAt(i);
    }
    html += "<br>Area: "+poly.Area()+" sq meters";
    var contentString = html;
    google.maps.event.addListener(poly,'click', function(event) {
        infowindow.setContent(contentString);
        if (event) {
            point = event.latLng;
        }
        infowindow.setPosition(point);
        infowindow.open(map);
    }); 
    if (!label) {
        label = "polyline #"+poly_num;
    }
    label = "<a href='javascript:google.maps.event.trigger(gpolys["+poly_num+"],\"click\");'>"+label+"</a>";
}

var centerPoint;
var current = false;
function initialize() {
    var myOptions = {
        zoom: 1,
        center: new google.maps.LatLng(userPoint[0].lat, userPoint[0].lng),
        mapTypeControl: true,
        navigationControl: true,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    map = new google.maps.Map(document.getElementById("map_canvas"),
            myOptions);

    google.maps.event.addListener(map, 'click', function() {
        infowindow.close();
    });

    bounds = new google.maps.LatLngBounds();
    bounds.extend(centerPoint);

    for (var i = 0; i < childPoint.length; i += 1) {
        bounds.extend(new google.maps.LatLng(childPoint[i].lat, childPoint[i].lng));
    }

    for (var i = 0; i < childPoint.length; i += 1) {

        geodesic = new google.maps.Polyline({
            path:[centerPoint, new google.maps.LatLng(childPoint[i].lat, childPoint[i].lng)],
                 strokeColor: "#40b553",
                 strokeOpacity: 0.8,
                 map: map,
                 geodesic:true,
                 strokeWeight: 4
        });

    }
    var content = "";

    createMarker(centerPoint, userPoint[0].id, "<img class='ui avatar image' src='"+userPoint[0].image+"'><span>"+userPoint[0].name+"</span>", false);

    for (var i = 0; i < childPoint.length; i += 1) {

        content = '<div class="ui card" style="padding: 5px;text-align: left;"><div class="card"><div class="content">'+
            '<img class="right floated mini ui image" src="'+childPoint[i].image+'">'+
            '<div class="header">'+childPoint[i].name+'</div><div class="meta">'+childPoint[i].location+'</div><div class="description">'+
            childPoint[i].thing+'</div></div></div><div class="short_Info"><a href="index.php?explore='+childPoint[i].thing+'">Learn more</a></div></div>';

        createMarker(new google.maps.LatLng(childPoint[i].lat, childPoint[i].lng), childPoint[i].id, content, true);

    }

    map.fitBounds(bounds);
}
if (navigator.geolocation) {

    navigator.geolocation.getCurrentPosition(function(position) {
        centerPoint = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
        initialize();
        bounds.extend(centerPoint);
    }, function() {
        centerPoint = new google.maps.LatLng(userPoint[0].lat, userPoint[0].lng);
        initialize();
        bounds.extend(centerPoint);
    });

} else {

    centerPoint = new google.maps.LatLng(userPoint[0].lat, userPoint[0].lng);
    initialize();
    bounds.extend(centerPoint);

}
