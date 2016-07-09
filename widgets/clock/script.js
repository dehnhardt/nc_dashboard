/**
 * CoolClock 2.1.4
 * Copyright 2010, Simon Baird
 * Released under the BSD License.
 *
 * Display an analog clock using canvas.
 * http://randomibis.com/coolclock/
 *
 */

// Constructor for CoolClock objects
window.CoolClock = function(options) {
    return this.init(options);
}

// Config contains some defaults, and clock skins
CoolClock.config = {
    tickDelay: 1000,
    longTickDelay: 15000,
    defaultRadius: 85,
    renderRadius: 100,
    defaultSkin: "chunkySwiss",
    // Should be in skin probably...
    // (TODO: allow skinning of digital display)
    showSecs: true,
    showAmPm: true,

    skins:	{
        // There are more skins in moreskins.js
        // Try making your own skin by copy/pasting one of these and tweaking it
        swissRail: {
            outerBorder: { lineWidth: 2, radius:95, color: "black", alpha: 1 },
            smallIndicator: { lineWidth: 2, startAt: 88, endAt: 92, color: "black", alpha: 1 },
            largeIndicator: { lineWidth: 4, startAt: 79, endAt: 92, color: "black", alpha: 1 },
            hourHand: { lineWidth: 8, startAt: -15, endAt: 50, color: "black", alpha: 1 },
            minuteHand: { lineWidth: 7, startAt: -15, endAt: 75, color: "black", alpha: 1 },
            secondHand: { lineWidth: 1, startAt: -20, endAt: 85, color: "red", alpha: 1 },
            secondDecoration: { lineWidth: 1, startAt: 70, radius: 4, fillColor: "red", color: "red", alpha: 1 }
        },
        chunkySwiss: {
            outerBorder: { lineWidth: 4, radius:97, color: "black", alpha: 1 },
            smallIndicator: { lineWidth: 4, startAt: 89, endAt: 93, color: "black", alpha: 1 },
            largeIndicator: { lineWidth: 8, startAt: 80, endAt: 93, color: "black", alpha: 1 },
            hourHand: { lineWidth: 12, startAt: -15, endAt: 60, color: "black", alpha: 1 },
            minuteHand: { lineWidth: 10, startAt: -15, endAt: 85, color: "black", alpha: 1 },
            secondHand: { lineWidth: 4, startAt: -20, endAt: 85, color: "red", alpha: 1 },
            secondDecoration: { lineWidth: 2, startAt: 70, radius: 8, fillColor: "red", color: "red", alpha: 1 }
        },
        chunkySwissOnBlack: {
            outerBorder: { lineWidth: 4, radius:97, color: "white", alpha: 1 },
            smallIndicator: { lineWidth: 4, startAt: 89, endAt: 93, color: "white", alpha: 1 },
            largeIndicator: { lineWidth: 8, startAt: 80, endAt: 93, color: "white", alpha: 1 },
            hourHand: { lineWidth: 12, startAt: -15, endAt: 60, color: "white", alpha: 1 },
            minuteHand: { lineWidth: 10, startAt: -15, endAt: 85, color: "white", alpha: 1 },
            secondHand: { lineWidth: 4, startAt: -20, endAt: 85, color: "red", alpha: 1 },
            secondDecoration: { lineWidth: 2, startAt: 70, radius: 8, fillColor: "red", color: "red", alpha: 1 }
        }

    },

    // Test for IE so we can nurse excanvas in a couple of places
    isIE: !!document.all,

    // Will store (a reference to) each clock here, indexed by the id of the canvas element
    clockTracker: {},

    // For giving a unique id to coolclock canvases with no id
    noIdCount: 0
};

// Define the CoolClock object's methods
CoolClock.prototype = {

    // Initialise using the parameters parsed from the colon delimited class
    init: function(options) {
        // Parse and store the options
        this.canvasId       = options.canvasId;
        this.skinId         = options.skinId || CoolClock.config.defaultSkin;
        this.displayRadius  = options.displayRadius || CoolClock.config.defaultRadius;
        this.showSecondHand = typeof options.showSecondHand == "boolean" ? options.showSecondHand : true;
        this.gmtOffset      = (options.gmtOffset != null && options.gmtOffset != '') ? parseFloat(options.gmtOffset) : null;
        this.showDigital    = typeof options.showDigital == "boolean" ? options.showDigital : false;
        this.logClock       = typeof options.logClock == "boolean" ? options.logClock : false;
        this.logClockRev    = typeof options.logClock == "boolean" ? options.logClockRev : false;

        this.tickDelay      = CoolClock.config[ this.showSecondHand ? "tickDelay" : "longTickDelay" ];

        // Get the canvas element
        this.canvas = document.getElementById(this.canvasId);

        // Make the canvas the requested size. It's always square.
        this.canvas.setAttribute("width",this.displayRadius*2);
        this.canvas.setAttribute("height",this.displayRadius*2);
        this.canvas.style.width = this.displayRadius*2 + "px";
        this.canvas.style.height = this.displayRadius*2 + "px";

        // Explain me please...?
        this.renderRadius = CoolClock.config.renderRadius;
        this.scale = this.displayRadius / this.renderRadius;

        // Initialise canvas context
        this.ctx = this.canvas.getContext("2d");
        this.ctx.scale(this.scale,this.scale);

        // Keep track of this object
        CoolClock.config.clockTracker[this.canvasId] = this;

        // Start the clock going
        this.tick();

        return this;
    },

    // Draw a circle at point x,y with params as defined in skin
    fullCircleAt: function(x,y,skin) {
        this.ctx.save();
        this.ctx.globalAlpha = skin.alpha;
        this.ctx.lineWidth = skin.lineWidth;

        if (!CoolClock.config.isIE) {
            this.ctx.beginPath();
        }

        if (CoolClock.config.isIE) {
            // excanvas doesn't scale line width so we will do it here
            this.ctx.lineWidth = this.ctx.lineWidth * this.scale;
        }

        this.ctx.arc(x, y, skin.radius, 0, 2*Math.PI, false);

        if (CoolClock.config.isIE) {
            // excanvas doesn't close the circle so let's fill in the tiny gap
            this.ctx.arc(x, y, skin.radius, -0.1, 0.1, false);
        }

        if (skin.fillColor) {
            this.ctx.fillStyle = skin.fillColor
            this.ctx.fill();
        }
        else {
            // XXX why not stroke and fill
            this.ctx.strokeStyle = skin.color;
            this.ctx.stroke();
        }
        this.ctx.restore();
    },

    // Draw some text centered vertically and horizontally
    drawTextAt: function(theText,x,y) {
        this.ctx.save();
        this.ctx.font = '15px sans-serif';
        var tSize = this.ctx.measureText(theText);
        if (!tSize.height) tSize.height = 15; // no height in firefox.. :(
        this.ctx.fillText(theText,x - tSize.width/2,y - tSize.height/2);
        this.ctx.restore();
    },

    lpad2: function(num) {
        return (num < 10 ? '0' : '') + num;
    },

    tickAngle: function(second) {
        // Log algorithm by David Bradshaw
        var tweak = 3; // If it's lower the one second mark looks wrong (?)
        if (this.logClock) {
            return second == 0 ? 0 : (Math.log(second*tweak) / Math.log(60*tweak));
        }
        else if (this.logClockRev) {
            // Flip the seconds then flip the angle (trickiness)
            second = (60 - second) % 60;
            return 1.0 - (second == 0 ? 0 : (Math.log(second*tweak) / Math.log(60*tweak)));
        }
        else {
            return second/60.0;
        }
    },

    timeText: function(hour,min,sec) {
        var c = CoolClock.config;
        return '' +
            (c.showAmPm ? ((hour%12)==0 ? 12 : (hour%12)) : hour) + ':' +
            this.lpad2(min) +
            (c.showSecs ? ':' + this.lpad2(sec) : '') +
            (c.showAmPm ? (hour < 12 ? ' am' : ' pm') : '')
            ;
    },

    // Draw a radial line by rotating then drawing a straight line
    // Ha ha, I think I've accidentally used Taus, (see http://tauday.com/)
    radialLineAtAngle: function(angleFraction,skin) {
        this.ctx.save();
        this.ctx.translate(this.renderRadius,this.renderRadius);
        this.ctx.rotate(Math.PI * (2.0 * angleFraction - 0.5));
        this.ctx.globalAlpha = skin.alpha;
        this.ctx.strokeStyle = skin.color;
        this.ctx.lineWidth = skin.lineWidth;

        if (CoolClock.config.isIE)
        // excanvas doesn't scale line width so we will do it here
            this.ctx.lineWidth = this.ctx.lineWidth * this.scale;

        if (skin.radius) {
            this.fullCircleAt(skin.startAt,0,skin)
        }
        else {
            this.ctx.beginPath();
            this.ctx.moveTo(skin.startAt,0)
            this.ctx.lineTo(skin.endAt,0);
            this.ctx.stroke();
        }
        this.ctx.restore();
    },

    render: function(hour,min,sec) {
        // Get the skin
        var skin = CoolClock.config.skins[this.skinId];
        if (!skin) skin = CoolClock.config.skins[CoolClock.config.defaultSkin];

        // Clear
        this.ctx.clearRect(0,0,this.renderRadius*2,this.renderRadius*2);

        // Draw the outer edge of the clock
        if (skin.outerBorder)
            this.fullCircleAt(this.renderRadius,this.renderRadius,skin.outerBorder);

        // Draw the tick marks. Every 5th one is a big one
        for (var i=0;i<60;i++) {
            (i%5)  && skin.smallIndicator && this.radialLineAtAngle(this.tickAngle(i),skin.smallIndicator);
            !(i%5) && skin.largeIndicator && this.radialLineAtAngle(this.tickAngle(i),skin.largeIndicator);
        }

        // Write the time
        if (this.showDigital) {
            this.drawTextAt(
                this.timeText(hour,min,sec),
                this.renderRadius,
                this.renderRadius+this.renderRadius/2
            );
        }

        // Draw the hands
        if (skin.hourHand)
            this.radialLineAtAngle(this.tickAngle(((hour%12)*5 + min/12.0)),skin.hourHand);

        if (skin.minuteHand)
            this.radialLineAtAngle(this.tickAngle((min + sec/60.0)),skin.minuteHand);

        if (this.showSecondHand && skin.secondHand)
            this.radialLineAtAngle(this.tickAngle(sec),skin.secondHand);

        // Second hand decoration doesn't render right in IE so lets turn it off
        if (!CoolClock.config.isIE && this.showSecondHand && skin.secondDecoration)
            this.radialLineAtAngle(this.tickAngle(sec),skin.secondDecoration);
    },

    // Check the time and display the clock
    refreshDisplay: function() {
        var now = new Date();
        if (this.gmtOffset != null) {
            // Use GMT + gmtOffset
            var offsetNow = new Date(now.valueOf() + (this.gmtOffset * 1000 * 60 * 60));
            this.render(offsetNow.getUTCHours(),offsetNow.getUTCMinutes(),offsetNow.getUTCSeconds());
        }
        else {
            // Use local time
            this.render(now.getHours(),now.getMinutes(),now.getSeconds());
        }
    },

    // Set timeout to trigger a tick in the future
    nextTick: function() {
        setTimeout("CoolClock.config.clockTracker['"+this.canvasId+"'].tick()",this.tickDelay);
    },

    // Check the canvas element hasn't been removed
    stillHere: function() {
        return document.getElementById(this.canvasId) != null;
    },

    // Main tick handler. Refresh the clock then setup the next tick
    tick: function() {
        if (this.stillHere()) {
            this.refreshDisplay()
            this.nextTick();
        }
    }
};

// Find all canvas elements that have the CoolClock class and turns them into clocks
CoolClock.findAndCreateClocks = function() {
    // (Let's not use a jQuery selector here so it's easier to use frameworks other than jQuery)
    var canvases = document.getElementsByTagName("canvas");
    for (var i=0;i<canvases.length;i++) {
        // Pull out the fields from the class. Example "CoolClock:chunkySwissOnBlack:1000"
        var fields = canvases[i].className.split(" ")[0].split(":");
        if (fields[0] == "CoolClock") {
            if (!canvases[i].id) {
                // If there's no id on this canvas element then give it one
                canvases[i].id = '_coolclock_auto_id_' + CoolClock.config.noIdCount++;
            }
            // Create a clock object for this element
            new CoolClock({
                canvasId:       canvases[i].id,
                skinId:         fields[1],
                displayRadius:  fields[2],
                showSecondHand: fields[3]!='noSeconds',
                gmtOffset:      fields[4],
                showDigital:    fields[5]=='showDigital',
                logClock:       fields[6]=='logClock',
                logClockRev:    fields[6]=='logClockRev'
            });
        }
    }
};

// If you don't have jQuery then you need a body onload like this: <body onload="CoolClock.findAndCreateClocks()">
// If you do have jQuery and it's loaded already then we can do it right now
if (window.jQuery) jQuery(document).ready(CoolClock.findAndCreateClocks);


// --------------------------------------------------------------------------------------------------------------------

CoolClock.config.skins = {

    swissRail: {
        outerBorder:      { lineWidth: 2, radius: 95, color: "black", alpha: 1 },
        smallIndicator:   { lineWidth: 2, startAt: 88, endAt: 92, color: "black", alpha: 1 },
        largeIndicator:   { lineWidth: 4, startAt: 79, endAt: 92, color: "black", alpha: 1 },
        hourHand:         { lineWidth: 8, startAt: -15, endAt: 50, color: "black", alpha: 1 },
        minuteHand:       { lineWidth: 7, startAt: -15, endAt: 75, color: "black", alpha: 1 },
        secondHand:       { lineWidth: 1, startAt: -20, endAt: 85, color: "red", alpha: 1 },
        secondDecoration: { lineWidth: 1, startAt: 70, radius: 4, fillColor: "red", color: "red", alpha: 1 }
    },

    chunkySwiss: {
        outerBorder:      { lineWidth: 4, radius: 97, color: "black", alpha: 1 },
        smallIndicator:   { lineWidth: 4, startAt: 89, endAt: 93, color: "black", alpha: 1 },
        largeIndicator:   { lineWidth: 8, startAt: 80, endAt: 93, color: "black", alpha: 1 },
        hourHand:         { lineWidth: 12, startAt: -15, endAt: 60, color: "black", alpha: 1 },
        minuteHand:       { lineWidth: 10, startAt: -15, endAt: 85, color: "black", alpha: 1 },
        secondHand:       { lineWidth: 4, startAt: -20, endAt: 85, color: "red", alpha: 1 },
        secondDecoration: { lineWidth: 2, startAt: 70, radius: 8, fillColor: "red", color: "red", alpha: 1 }
    },

    chunkySwissOnBlack: {
        outerBorder:      { lineWidth: 4, radius: 97, color: "white", alpha: 1 },
        smallIndicator:   { lineWidth: 4, startAt: 89, endAt: 93, color: "white", alpha: 1 },
        largeIndicator:   { lineWidth: 8, startAt: 80, endAt: 93, color: "white", alpha: 1 },
        hourHand:         { lineWidth: 12, startAt: -15, endAt: 60, color: "white", alpha: 1 },
        minuteHand:       { lineWidth: 10, startAt: -15, endAt: 85, color: "white", alpha: 1 },
        secondHand:       { lineWidth: 4, startAt: -20, endAt: 85, color: "red", alpha: 1 },
        secondDecoration: { lineWidth: 2, startAt: 70, radius: 8, fillColor: "red", color: "red", alpha: 1 }
    },

    fancy: {
        outerBorder:      { lineWidth: 5, radius: 95, color: "green", alpha: 0.7 },
        smallIndicator:   { lineWidth: 1, startAt: 80, endAt: 93, color: "black", alpha: 0.4 },
        largeIndicator:   { lineWidth: 1, startAt: 30, endAt: 93, color: "black", alpha: 0.5 },
        hourHand:         { lineWidth: 8, startAt: -15, endAt: 50, color: "blue", alpha: 0.7 },
        minuteHand:       { lineWidth: 7, startAt: -15, endAt: 92, color: "red", alpha: 0.7 },
        secondHand:       { lineWidth: 10, startAt: 80, endAt: 85, color: "blue", alpha: 0.3 },
        secondDecoration: { lineWidth: 1, startAt: 30, radius: 50, fillColor: "blue", color: "red", alpha: 0.15 }
    },

    machine: {
        outerBorder:      { lineWidth: 60, radius: 55, color: "#dd6655", alpha: 1 },
        smallIndicator:   { lineWidth: 4, startAt: 80, endAt: 95, color: "white", alpha: 1 },
        largeIndicator:   { lineWidth: 14, startAt: 77, endAt: 92, color: "#dd6655", alpha: 1 },
        hourHand:         { lineWidth: 18, startAt: -15, endAt: 40, color: "white", alpha: 1 },
        minuteHand:       { lineWidth: 14, startAt: 24, endAt: 100, color: "#771100", alpha: 0.5 },
        secondHand:       { lineWidth: 3, startAt: 22, endAt: 83, color: "green", alpha: 0 },
        secondDecoration: { lineWidth: 1, startAt: 52, radius: 26, fillColor: "#ffcccc", color: "red", alpha: 0.5 }
    },

    simonbaird_com: {
        hourHand:         { lineWidth: 80, startAt: -15, endAt: 35,  color: 'magenta', alpha: 0.5 },
        minuteHand:       { lineWidth: 80, startAt: -15, endAt: 65,  color: 'cyan', alpha: 0.5 },
        secondDecoration: { lineWidth: 1,  startAt: 40,  radius: 40, color: "#fff", fillColor: 'yellow', alpha: 0.5 }
    },

    // by bonstio, http://bonstio.net
    classic/*was gIG*/: {
        outerBorder:      { lineWidth: 185, radius: 1, color: "#E5ECF9", alpha: 1 },
        smallIndicator:   { lineWidth: 2, startAt: 89, endAt: 94, color: "#3366CC", alpha: 1 },
        largeIndicator:   { lineWidth: 4, startAt: 83, endAt: 94, color: "#3366CC", alpha: 1 },
        hourHand:         { lineWidth: 5, startAt: 0, endAt: 60, color: "black", alpha: 1 },
        minuteHand:       { lineWidth: 4, startAt: 0, endAt: 80, color: "black", alpha: 1 },
        secondHand:       { lineWidth: 1, startAt: -20, endAt: 85, color: "red", alpha: .85 },
        secondDecoration: { lineWidth: 3, startAt: 0, radius: 2, fillColor: "black", color: "black", alpha: 1 }
    },

    modern/*was gIG2*/: {
        outerBorder:      { lineWidth: 185, radius: 1, color: "#E5ECF9", alpha: 1 },
        smallIndicator:   { lineWidth: 5, startAt: 88, endAt: 94, color: "#3366CC", alpha: 1 },
        largeIndicator:   { lineWidth: 5, startAt: 88, endAt: 94, color: "#3366CC", alpha: 1 },
        hourHand:         { lineWidth: 8, startAt: 0, endAt: 60, color: "black", alpha: 1 },
        minuteHand:       { lineWidth: 8, startAt: 0, endAt: 80, color: "black", alpha: 1 },
        secondHand:       { lineWidth: 5, startAt: 80, endAt: 85, color: "red", alpha: .85 },
        secondDecoration: { lineWidth: 3, startAt: 0, radius: 4, fillColor: "black", color: "black", alpha: 1 }
    },

    simple/*was gIG3*/: {
        outerBorder:      { lineWidth: 185, radius: 1, color: "#E5ECF9", alpha: 1 },
        smallIndicator:   { lineWidth: 10, startAt: 90, endAt: 94, color: "#3366CC", alpha: 1 },
        largeIndicator:   { lineWidth: 10, startAt: 90, endAt: 94, color: "#3366CC", alpha: 1 },
        hourHand:         { lineWidth: 8, startAt: 0, endAt: 60, color: "black", alpha: 1 },
        minuteHand:       { lineWidth: 8, startAt: 0, endAt: 80, color: "black", alpha: 1 },
        secondHand:       { lineWidth: 5, startAt: 80, endAt: 85, color: "red", alpha: .85 },
        secondDecoration: { lineWidth: 3, startAt: 0, radius: 4, fillColor: "black", color: "black", alpha: 1 }
    },

    // by securephp
    securephp: {
        outerBorder:      { lineWidth: 100, radius: 0.45, color: "#669900", alpha: 0.3 },
        smallIndicator:   { lineWidth: 2, startAt: 80, endAt: 90 , color: "green", alpha: 1 },
        largeIndicator:   { lineWidth: 8.5, startAt: 20, endAt: 40 , color: "green", alpha: 0.4 },
        hourHand:         { lineWidth: 3, startAt: 0, endAt: 60, color: "black", alpha: 1 },
        minuteHand:       { lineWidth: 2, startAt: 0, endAt: 75, color: "black", alpha: 1 },
        secondHand:       { lineWidth: 1, startAt: -10, endAt: 80, color: "blue", alpha: 0.8 },
        secondDecoration: { lineWidth: 1, startAt: 70, radius: 4, fillColor: "blue", color: "red", alpha: 1 }
    },

    Tes2: {
        outerBorder:      { lineWidth: 4, radius: 95, color: "black", alpha: 0.5 },
        smallIndicator:   { lineWidth: 1, startAt: 10, endAt: 50 , color: "#66CCFF", alpha: 1 },
        largeIndicator:   { lineWidth: 8.5, startAt: 60, endAt: 70, color: "#6699FF", alpha: 1 },
        hourHand:         { lineWidth: 5, startAt: -15, endAt: 60, color: "black", alpha: 0.7 },
        minuteHand:       { lineWidth: 3, startAt: -25, endAt: 75, color: "black", alpha: 0.7 },
        secondHand:       { lineWidth: 1.5, startAt: -20, endAt: 88, color: "red", alpha: 1 },
        secondDecoration: { lineWidth: 1, startAt: 20, radius: 4, fillColor: "blue", color: "red", alpha: 1 }
    },


    Lev: {
        outerBorder:      { lineWidth: 10, radius: 95, color: "#CCFF33", alpha: 0.65 },
        smallIndicator:   { lineWidth: 5, startAt: 84, endAt: 90, color: "#996600", alpha: 1 },
        largeIndicator:   { lineWidth: 40, startAt: 25, endAt: 95, color: "#336600", alpha: 0.55 },
        hourHand:         { lineWidth: 4, startAt: 0, endAt: 65, color: "black", alpha: 0.9 },
        minuteHand:       { lineWidth: 3, startAt: 0, endAt: 80, color: "black", alpha: 0.85 },
        secondHand:       { lineWidth: 1, startAt: 0, endAt: 85, color: "black", alpha: 1 },
        secondDecoration: { lineWidth: 2, startAt: 5, radius: 10, fillColor: "black", color: "black", alpha: 1 }
    },

    Sand: {
        outerBorder:      { lineWidth: 1, radius: 70, color: "black", alpha: 0.5 },
        smallIndicator:   { lineWidth: 3, startAt: 50, endAt: 70, color: "#0066FF", alpha: 0.5 },
        largeIndicator:   { lineWidth: 200, startAt: 80, endAt: 95, color: "#996600", alpha: 0.75 },
        hourHand:         { lineWidth: 4, startAt: 0, endAt: 65, color: "black", alpha: 0.9 },
        minuteHand:       { lineWidth: 3, startAt: 0, endAt: 80, color: "black", alpha: 0.85 },
        secondHand:       { lineWidth: 1, startAt: 0, endAt: 85, color: "black", alpha: 1 },
        secondDecoration: { lineWidth: 2, startAt: 5, radius: 10, fillColor: "black", color: "black", alpha: 1 }
    },

    Sun: {
        outerBorder:      { lineWidth: 100, radius: 140, color: "#99FFFF", alpha: 0.2 },
        smallIndicator:   { lineWidth: 300, startAt: 50, endAt: 70, color: "black", alpha: 0.1 },
        largeIndicator:   { lineWidth: 5, startAt: 80, endAt: 95, color: "black", alpha: 0.65 },
        hourHand:         { lineWidth: 4, startAt: 0, endAt: 65, color: "black", alpha: 0.9 },
        minuteHand:       { lineWidth: 3, startAt: 0, endAt: 80, color: "black", alpha: 0.85 },
        secondHand:       { lineWidth: 1, startAt: 0, endAt: 90, color: "black", alpha: 1 },
        secondDecoration: { lineWidth: 2, startAt: 5, radius: 10, fillColor: "black", color: "black", alpha: 1 }
    },

    Tor: {
        outerBorder:      { lineWidth: 10, radius: 88, color: "#996600", alpha: 0.9 },
        smallIndicator:   { lineWidth: 6, startAt: -10, endAt: 73, color: "green", alpha: 0.3 },
        largeIndicator:   { lineWidth: 6, startAt: 73, endAt: 100, color: "black", alpha: 0.65 },
        hourHand:         { lineWidth: 4, startAt: 0, endAt: 65, color: "black", alpha: 1 },
        minuteHand:       { lineWidth: 3, startAt: 0, endAt: 80, color: "black", alpha: 1 },
        secondHand:       { lineWidth: 1, startAt: -73, endAt: 73, color: "black", alpha: 0.8 },
        secondDecoration: { lineWidth: 2, startAt: 5, radius: 10, fillColor: "black", color: "black", alpha: 1 }
    },

    Cold: {
        outerBorder:      { lineWidth: 15, radius: 90, color: "black", alpha: 0.3 },
        smallIndicator:   { lineWidth: 15, startAt: -10, endAt: 95, color: "blue", alpha: 0.1 },
        largeIndicator:   { lineWidth: 3, startAt: 80, endAt: 95, color: "blue", alpha: 0.65 },
        hourHand:         { lineWidth: 4, startAt: 0, endAt: 65, color: "black", alpha: 1 },
        minuteHand:       { lineWidth: 3, startAt: 0, endAt: 80, color: "black", alpha: 1 },
        secondHand:       { lineWidth: 1, startAt: 0, endAt: 85, color: "black", alpha: 0.8 },
        secondDecoration: { lineWidth: 5, startAt: 30, radius: 10, fillColor: "black", color: "black", alpha: 1 }
    },

    Babosa: {
        outerBorder:      { lineWidth: 100, radius: 25, color: "blue", alpha: 0.25 },
        smallIndicator:   { lineWidth: 3, startAt: 90, endAt: 95, color: "#3366CC", alpha: 1 },
        largeIndicator:   { lineWidth: 4, startAt: 75, endAt: 95, color: "#3366CC", alpha: 1 },
        hourHand:         { lineWidth: 4, startAt: 0, endAt: 60, color: "black", alpha: 1 },
        minuteHand:       { lineWidth: 3, startAt: 0, endAt: 85, color: "black", alpha: 1 },
        secondHand:       { lineWidth: 12, startAt: 75, endAt: 90, color: "red", alpha: 0.8 },
        secondDecoration: { lineWidth: 3, startAt: 0, radius: 4, fillColor: "black", color: "black", alpha: 1 }
    },

    Tumb: {
        outerBorder:      { lineWidth: 105, radius: 5, color: "green", alpha: 0.4 },
        smallIndicator:   { lineWidth: 1, startAt: 93, endAt: 98, color: "green", alpha: 1 },
        largeIndicator:   { lineWidth: 50, startAt: 0, endAt: 89, color: "red", alpha: 0.14 },
        hourHand:         { lineWidth: 4, startAt: 0, endAt: 65, color: "black", alpha: 1 },
        minuteHand:       { lineWidth: 3, startAt: 0, endAt: 80, color: "black", alpha: 1 },
        secondHand:       { lineWidth: 1, startAt: 0, endAt: 85, color: "black", alpha: 0.8 },
        secondDecoration: { lineWidth: 5, startAt: 50, radius: 90, fillColor: "black", color: "black", alpha: 0.05 }
    },

    Stone: {
        outerBorder:      { lineWidth: 15, radius: 80, color: "#339933", alpha: 0.5 },
        smallIndicator:   { lineWidth: 2, startAt: 70, endAt: 90, color: "#FF3300", alpha: 0.7 },
        largeIndicator:   { lineWidth: 15, startAt: 0, endAt: 29, color: "#FF6600", alpha: 0.3 },
        hourHand:         { lineWidth: 4, startAt: 0, endAt: 65, color: "black", alpha: 1 },
        minuteHand:       { lineWidth: 3, startAt: 0, endAt: 75, color: "black", alpha: 1 },
        secondHand:       { lineWidth: 1, startAt: 0, endAt: 85, color: "black", alpha: 0.8 },
        secondDecoration: { lineWidth: 5, startAt: 50, radius: 90, fillColor: "black", color: "black", alpha: 0.05 }
    },

    Disc: {
        outerBorder:      { lineWidth: 105, radius: 1, color: "#666600", alpha: 0.2 },
        smallIndicator:   { lineWidth: 1, startAt: 58, endAt: 95, color: "#669900", alpha: 0.8 },
        largeIndicator:   { lineWidth: 6, startAt: 25, endAt: 35, color: "#666600", alpha: 1 },
        hourHand:         { lineWidth: 4, startAt: 0, endAt: 65, color: "black", alpha: 1 },
        minuteHand:       { lineWidth: 3, startAt: 0, endAt: 75, color: "black", alpha: 1 },
        secondHand:       { lineWidth: 1, startAt: -75, endAt: 75, color: "#99CC00", alpha: 0.8 },
        secondDecoration: { lineWidth: 5, startAt: 50, radius: 90, fillColor: "#00FF00", color: "green", alpha: 0.05 }
    },

    // By Yoo Nhe
    watermelon: {
        outerBorder:      { lineWidth: 100, radius: 1.7, color: "#d93d04", alpha: 5 },
        smallIndicator:   { lineWidth: 2, startAt: 50, endAt: 70, color: "#d93d04", alpha: 5 },
        largeIndicator:   { lineWidth: 2, startAt: 45, endAt: 94, color: "#a9bf04", alpha: 1 },
        hourHand:         { lineWidth: 5, startAt: -20, endAt: 80, color: "#8c0d17", alpha: 1 },
        minuteHand:       { lineWidth: 2, startAt: -20, endAt: 80, color: "#7c8c03", alpha: .9 },
        secondHand:       { lineWidth: 2, startAt: 70, endAt: 94, color: "#d93d04", alpha: .85 },
        secondDecoration: { lineWidth: 1, startAt: 70, radius: 3, fillColor: "red", color: "black", alpha: .7 }
    }
};


// --------------------------------------------------------------------------------------------------------------------

// Copyright 2006 Google Inc.
//
// Licensed under the Apache License, Version 2.0 (the "License");
// you may not use this file except in compliance with the License.
// You may obtain a copy of the License at
//
//   http://www.apache.org/licenses/LICENSE-2.0
//
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.


// Known Issues:
//
// * Patterns are not implemented.
// * Radial gradient are not implemented. The VML version of these look very
//   different from the canvas one.
// * Clipping paths are not implemented.
// * Coordsize. The width and height attribute have higher priority than the
//   width and height style values which isn't correct.
// * Painting mode isn't implemented.
// * Canvas width/height should is using content-box by default. IE in
//   Quirks mode will draw the canvas using border-box. Either change your
//   doctype to HTML5
//   (http://www.whatwg.org/specs/web-apps/current-work/#the-doctype)
//   or use Box Sizing Behavior from WebFX
//   (http://webfx.eae.net/dhtml/boxsizing/boxsizing.html)
// * Optimize. There is always room for speed improvements.

// only add this code if we do not already have a canvas implementation
if (!window.CanvasRenderingContext2D) {

    (function () {

        // alias some functions to make (compiled) code shorter
        var m = Math;
        var mr = m.round;
        var ms = m.sin;
        var mc = m.cos;

        // this is used for sub pixel precision
        var Z = 10;
        var Z2 = Z / 2;

        var G_vmlCanvasManager_ = {
            init: function (opt_doc) {
                var doc = opt_doc || document;
                if (/MSIE/.test(navigator.userAgent) && !window.opera) {
                    var self = this;
                    doc.attachEvent("onreadystatechange", function () {
                        self.init_(doc);
                    });
                }
            },

            init_: function (doc) {
                if (doc.readyState == "complete") {
                    // create xmlns
                    if (!doc.namespaces["g_vml_"]) {
                        doc.namespaces.add("g_vml_", "urn:schemas-microsoft-com:vml");
                    }

                    // setup default css
                    var ss = doc.createStyleSheet();
                    ss.cssText = "canvas{display:inline-block;overflow:hidden;" +
                        // default size is 300x150 in Gecko and Opera
                    "text-align:left;width:300px;height:150px}" +
                    "g_vml_\\:*{behavior:url(#default#VML)}";

                    // find all canvas elements
                    var els = doc.getElementsByTagName("canvas");
                    for (var i = 0; i < els.length; i++) {
                        if (!els[i].getContext) {
                            this.initElement(els[i]);
                        }
                    }
                }
            },

            fixElement_: function (el) {
                // in IE before version 5.5 we would need to add HTML: to the tag name
                // but we do not care about IE before version 6
                var outerHTML = el.outerHTML;

                var newEl = el.ownerDocument.createElement(outerHTML);
                // if the tag is still open IE has created the children as siblings and
                // it has also created a tag with the name "/FOO"
                if (outerHTML.slice(-2) != "/>") {
                    var tagName = "/" + el.tagName;
                    var ns;
                    // remove content
                    while ((ns = el.nextSibling) && ns.tagName != tagName) {
                        ns.removeNode();
                    }
                    // remove the incorrect closing tag
                    if (ns) {
                        ns.removeNode();
                    }
                }
                el.parentNode.replaceChild(newEl, el);
                return newEl;
            },

            /**
             * Public initializes a canvas element so that it can be used as canvas
             * element from now on. This is called automatically before the page is
             * loaded but if you are creating elements using createElement you need to
             * make sure this is called on the element.
             * @param {HTMLElement} el The canvas element to initialize.
             * @return {HTMLElement} the element that was created.
             */
            initElement: function (el) {
                el = this.fixElement_(el);
                el.getContext = function () {
                    if (this.context_) {
                        return this.context_;
                    }
                    return this.context_ = new CanvasRenderingContext2D_(this);
                };

                // do not use inline function because that will leak memory
                el.attachEvent('onpropertychange', onPropertyChange);
                el.attachEvent('onresize', onResize);

                var attrs = el.attributes;
                if (attrs.width && attrs.width.specified) {
                    // TODO: use runtimeStyle and coordsize
                    // el.getContext().setWidth_(attrs.width.nodeValue);
                    el.style.width = attrs.width.nodeValue + "px";
                } else {
                    el.width = el.clientWidth;
                }
                if (attrs.height && attrs.height.specified) {
                    // TODO: use runtimeStyle and coordsize
                    // el.getContext().setHeight_(attrs.height.nodeValue);
                    el.style.height = attrs.height.nodeValue + "px";
                } else {
                    el.height = el.clientHeight;
                }
                //el.getContext().setCoordsize_()
                return el;
            }
        };

        function onPropertyChange(e) {
            var el = e.srcElement;

            switch (e.propertyName) {
                case 'width':
                    el.style.width = el.attributes.width.nodeValue + "px";
                    el.getContext().clearRect();
                    break;
                case 'height':
                    el.style.height = el.attributes.height.nodeValue + "px";
                    el.getContext().clearRect();
                    break;
            }
        }

        function onResize(e) {
            var el = e.srcElement;
            if (el.firstChild) {
                el.firstChild.style.width =  el.clientWidth + 'px';
                el.firstChild.style.height = el.clientHeight + 'px';
            }
        }

        G_vmlCanvasManager_.init();

        // precompute "00" to "FF"
        var dec2hex = [];
        for (var i = 0; i < 16; i++) {
            for (var j = 0; j < 16; j++) {
                dec2hex[i * 16 + j] = i.toString(16) + j.toString(16);
            }
        }

        function createMatrixIdentity() {
            return [
                [1, 0, 0],
                [0, 1, 0],
                [0, 0, 1]
            ];
        }

        function matrixMultiply(m1, m2) {
            var result = createMatrixIdentity();

            for (var x = 0; x < 3; x++) {
                for (var y = 0; y < 3; y++) {
                    var sum = 0;

                    for (var z = 0; z < 3; z++) {
                        sum += m1[x][z] * m2[z][y];
                    }

                    result[x][y] = sum;
                }
            }
            return result;
        }

        function copyState(o1, o2) {
            o2.fillStyle     = o1.fillStyle;
            o2.lineCap       = o1.lineCap;
            o2.lineJoin      = o1.lineJoin;
            o2.lineWidth     = o1.lineWidth;
            o2.miterLimit    = o1.miterLimit;
            o2.shadowBlur    = o1.shadowBlur;
            o2.shadowColor   = o1.shadowColor;
            o2.shadowOffsetX = o1.shadowOffsetX;
            o2.shadowOffsetY = o1.shadowOffsetY;
            o2.strokeStyle   = o1.strokeStyle;
            o2.arcScaleX_    = o1.arcScaleX_;
            o2.arcScaleY_    = o1.arcScaleY_;
        }

        function processStyle(styleString) {
            var str, alpha = 1;

            styleString = String(styleString);
            if (styleString.substring(0, 3) == "rgb") {
                var start = styleString.indexOf("(", 3);
                var end = styleString.indexOf(")", start + 1);
                var guts = styleString.substring(start + 1, end).split(",");

                str = "#";
                for (var i = 0; i < 3; i++) {
                    str += dec2hex[Number(guts[i])];
                }

                if ((guts.length == 4) && (styleString.substr(3, 1) == "a")) {
                    alpha = guts[3];
                }
            } else {
                str = styleString;
            }

            return [str, alpha];
        }

        function processLineCap(lineCap) {
            switch (lineCap) {
                case "butt":
                    return "flat";
                case "round":
                    return "round";
                case "square":
                default:
                    return "square";
            }
        }

        /**
         * This class implements CanvasRenderingContext2D interface as described by
         * the WHATWG.
         * @param {HTMLElement} surfaceElement The element that the 2D context should
         * be associated with
         */
        function CanvasRenderingContext2D_(surfaceElement) {
            this.m_ = createMatrixIdentity();

            this.mStack_ = [];
            this.aStack_ = [];
            this.currentPath_ = [];

            // Canvas context properties
            this.strokeStyle = "#000";
            this.fillStyle = "#000";

            this.lineWidth = 1;
            this.lineJoin = "miter";
            this.lineCap = "butt";
            this.miterLimit = Z * 1;
            this.globalAlpha = 1;
            this.canvas = surfaceElement;

            var el = surfaceElement.ownerDocument.createElement('div');
            el.style.width =  surfaceElement.clientWidth + 'px';
            el.style.height = surfaceElement.clientHeight + 'px';
            el.style.overflow = 'hidden';
            el.style.position = 'absolute';
            surfaceElement.appendChild(el);

            this.element_ = el;
            this.arcScaleX_ = 1;
            this.arcScaleY_ = 1;
        };

        var contextPrototype = CanvasRenderingContext2D_.prototype;
        contextPrototype.clearRect = function() {
            this.element_.innerHTML = "";
            this.currentPath_ = [];
        };

        contextPrototype.beginPath = function() {
            // TODO: Branch current matrix so that save/restore has no effect
            //       as per safari docs.

            this.currentPath_ = [];
        };

        contextPrototype.moveTo = function(aX, aY) {
            this.currentPath_.push({type: "moveTo", x: aX, y: aY});
            this.currentX_ = aX;
            this.currentY_ = aY;
        };

        contextPrototype.lineTo = function(aX, aY) {
            this.currentPath_.push({type: "lineTo", x: aX, y: aY});
            this.currentX_ = aX;
            this.currentY_ = aY;
        };

        contextPrototype.bezierCurveTo = function(aCP1x, aCP1y,
                                                  aCP2x, aCP2y,
                                                  aX, aY) {
            this.currentPath_.push({type: "bezierCurveTo",
                cp1x: aCP1x,
                cp1y: aCP1y,
                cp2x: aCP2x,
                cp2y: aCP2y,
                x: aX,
                y: aY});
            this.currentX_ = aX;
            this.currentY_ = aY;
        };

        contextPrototype.quadraticCurveTo = function(aCPx, aCPy, aX, aY) {
            // the following is lifted almost directly from
            // http://developer.mozilla.org/en/docs/Canvas_tutorial:Drawing_shapes
            var cp1x = this.currentX_ + 2.0 / 3.0 * (aCPx - this.currentX_);
            var cp1y = this.currentY_ + 2.0 / 3.0 * (aCPy - this.currentY_);
            var cp2x = cp1x + (aX - this.currentX_) / 3.0;
            var cp2y = cp1y + (aY - this.currentY_) / 3.0;
            this.bezierCurveTo(cp1x, cp1y, cp2x, cp2y, aX, aY);
        };

        contextPrototype.arc = function(aX, aY, aRadius,
                                        aStartAngle, aEndAngle, aClockwise) {
            aRadius *= Z;
            var arcType = aClockwise ? "at" : "wa";

            var xStart = aX + (mc(aStartAngle) * aRadius) - Z2;
            var yStart = aY + (ms(aStartAngle) * aRadius) - Z2;

            var xEnd = aX + (mc(aEndAngle) * aRadius) - Z2;
            var yEnd = aY + (ms(aEndAngle) * aRadius) - Z2;

            // IE won't render arches drawn counter clockwise if xStart == xEnd.
            if (xStart == xEnd && !aClockwise) {
                xStart += 0.125; // Offset xStart by 1/80 of a pixel. Use something
                                 // that can be represented in binary
            }

            this.currentPath_.push({type: arcType,
                x: aX,
                y: aY,
                radius: aRadius,
                xStart: xStart,
                yStart: yStart,
                xEnd: xEnd,
                yEnd: yEnd});

        };

        contextPrototype.rect = function(aX, aY, aWidth, aHeight) {
            this.moveTo(aX, aY);
            this.lineTo(aX + aWidth, aY);
            this.lineTo(aX + aWidth, aY + aHeight);
            this.lineTo(aX, aY + aHeight);
            this.closePath();
        };

        contextPrototype.strokeRect = function(aX, aY, aWidth, aHeight) {
            // Will destroy any existing path (same as FF behaviour)
            this.beginPath();
            this.moveTo(aX, aY);
            this.lineTo(aX + aWidth, aY);
            this.lineTo(aX + aWidth, aY + aHeight);
            this.lineTo(aX, aY + aHeight);
            this.closePath();
            this.stroke();
        };

        contextPrototype.fillRect = function(aX, aY, aWidth, aHeight) {
            // Will destroy any existing path (same as FF behaviour)
            this.beginPath();
            this.moveTo(aX, aY);
            this.lineTo(aX + aWidth, aY);
            this.lineTo(aX + aWidth, aY + aHeight);
            this.lineTo(aX, aY + aHeight);
            this.closePath();
            this.fill();
        };

        contextPrototype.createLinearGradient = function(aX0, aY0, aX1, aY1) {
            var gradient = new CanvasGradient_("gradient");
            return gradient;
        };

        contextPrototype.createRadialGradient = function(aX0, aY0,
                                                         aR0, aX1,
                                                         aY1, aR1) {
            var gradient = new CanvasGradient_("gradientradial");
            gradient.radius1_ = aR0;
            gradient.radius2_ = aR1;
            gradient.focus_.x = aX0;
            gradient.focus_.y = aY0;
            return gradient;
        };

        contextPrototype.drawImage = function (image, var_args) {
            var dx, dy, dw, dh, sx, sy, sw, sh;

            // to find the original width we overide the width and height
            var oldRuntimeWidth = image.runtimeStyle.width;
            var oldRuntimeHeight = image.runtimeStyle.height;
            image.runtimeStyle.width = 'auto';
            image.runtimeStyle.height = 'auto';

            // get the original size
            var w = image.width;
            var h = image.height;

            // and remove overides
            image.runtimeStyle.width = oldRuntimeWidth;
            image.runtimeStyle.height = oldRuntimeHeight;

            if (arguments.length == 3) {
                dx = arguments[1];
                dy = arguments[2];
                sx = sy = 0;
                sw = dw = w;
                sh = dh = h;
            } else if (arguments.length == 5) {
                dx = arguments[1];
                dy = arguments[2];
                dw = arguments[3];
                dh = arguments[4];
                sx = sy = 0;
                sw = w;
                sh = h;
            } else if (arguments.length == 9) {
                sx = arguments[1];
                sy = arguments[2];
                sw = arguments[3];
                sh = arguments[4];
                dx = arguments[5];
                dy = arguments[6];
                dw = arguments[7];
                dh = arguments[8];
            } else {
                throw "Invalid number of arguments";
            }

            var d = this.getCoords_(dx, dy);

            var w2 = sw / 2;
            var h2 = sh / 2;

            var vmlStr = [];

            var W = 10;
            var H = 10;

            // For some reason that I've now forgotten, using divs didn't work
            vmlStr.push(' <g_vml_:group',
                ' coordsize="', Z * W, ',', Z * H, '"',
                ' coordorigin="0,0"' ,
                ' style="width:', W, ';height:', H, ';position:absolute;');

            // If filters are necessary (rotation exists), create them
            // filters are bog-slow, so only create them if abbsolutely necessary
            // The following check doesn't account for skews (which don't exist
            // in the canvas spec (yet) anyway.

            if (this.m_[0][0] != 1 || this.m_[0][1]) {
                var filter = [];

                // Note the 12/21 reversal
                filter.push("M11='", this.m_[0][0], "',",
                    "M12='", this.m_[1][0], "',",
                    "M21='", this.m_[0][1], "',",
                    "M22='", this.m_[1][1], "',",
                    "Dx='", mr(d.x / Z), "',",
                    "Dy='", mr(d.y / Z), "'");

                // Bounding box calculation (need to minimize displayed area so that
                // filters don't waste time on unused pixels.
                var max = d;
                var c2 = this.getCoords_(dx + dw, dy);
                var c3 = this.getCoords_(dx, dy + dh);
                var c4 = this.getCoords_(dx + dw, dy + dh);

                max.x = Math.max(max.x, c2.x, c3.x, c4.x);
                max.y = Math.max(max.y, c2.y, c3.y, c4.y);

                vmlStr.push("padding:0 ", mr(max.x / Z), "px ", mr(max.y / Z),
                    "px 0;filter:progid:DXImageTransform.Microsoft.Matrix(",
                    filter.join(""), ", sizingmethod='clip');")
            } else {
                vmlStr.push("top:", mr(d.y / Z), "px;left:", mr(d.x / Z), "px;")
            }

            vmlStr.push(' ">' ,
                '<g_vml_:image src="', image.src, '"',
                ' style="width:', Z * dw, ';',
                ' height:', Z * dh, ';"',
                ' cropleft="', sx / w, '"',
                ' croptop="', sy / h, '"',
                ' cropright="', (w - sx - sw) / w, '"',
                ' cropbottom="', (h - sy - sh) / h, '"',
                ' />',
                '</g_vml_:group>');

            this.element_.insertAdjacentHTML("BeforeEnd",
                vmlStr.join(""));
        };

        contextPrototype.stroke = function(aFill) {
            var lineStr = [];
            var lineOpen = false;
            var a = processStyle(aFill ? this.fillStyle : this.strokeStyle);
            var color = a[0];
            var opacity = a[1] * this.globalAlpha;

            var W = 10;
            var H = 10;

            lineStr.push('<g_vml_:shape',
                ' fillcolor="', color, '"',
                ' filled="', Boolean(aFill), '"',
                ' style="position:absolute;width:', W, ';height:', H, ';"',
                ' coordorigin="0 0" coordsize="', Z * W, ' ', Z * H, '"',
                ' stroked="', !aFill, '"',
                ' strokeweight="', this.lineWidth, '"',
                ' strokecolor="', color, '"',
                ' path="');

            var newSeq = false;
            var min = {x: null, y: null};
            var max = {x: null, y: null};

            for (var i = 0; i < this.currentPath_.length; i++) {
                var p = this.currentPath_[i];

                if (p.type == "moveTo") {
                    lineStr.push(" m ");
                    var c = this.getCoords_(p.x, p.y);
                    lineStr.push(mr(c.x), ",", mr(c.y));
                } else if (p.type == "lineTo") {
                    lineStr.push(" l ");
                    var c = this.getCoords_(p.x, p.y);
                    lineStr.push(mr(c.x), ",", mr(c.y));
                } else if (p.type == "close") {
                    lineStr.push(" x ");
                } else if (p.type == "bezierCurveTo") {
                    lineStr.push(" c ");
                    var c = this.getCoords_(p.x, p.y);
                    var c1 = this.getCoords_(p.cp1x, p.cp1y);
                    var c2 = this.getCoords_(p.cp2x, p.cp2y);
                    lineStr.push(mr(c1.x), ",", mr(c1.y), ",",
                        mr(c2.x), ",", mr(c2.y), ",",
                        mr(c.x), ",", mr(c.y));
                } else if (p.type == "at" || p.type == "wa") {
                    lineStr.push(" ", p.type, " ");
                    var c  = this.getCoords_(p.x, p.y);
                    var cStart = this.getCoords_(p.xStart, p.yStart);
                    var cEnd = this.getCoords_(p.xEnd, p.yEnd);

                    lineStr.push(mr(c.x - this.arcScaleX_ * p.radius), ",",
                        mr(c.y - this.arcScaleY_ * p.radius), " ",
                        mr(c.x + this.arcScaleX_ * p.radius), ",",
                        mr(c.y + this.arcScaleY_ * p.radius), " ",
                        mr(cStart.x), ",", mr(cStart.y), " ",
                        mr(cEnd.x), ",", mr(cEnd.y));
                }


                // TODO: Following is broken for curves due to
                //       move to proper paths.

                // Figure out dimensions so we can do gradient fills
                // properly
                if(c) {
                    if (min.x == null || c.x < min.x) {
                        min.x = c.x;
                    }
                    if (max.x == null || c.x > max.x) {
                        max.x = c.x;
                    }
                    if (min.y == null || c.y < min.y) {
                        min.y = c.y;
                    }
                    if (max.y == null || c.y > max.y) {
                        max.y = c.y;
                    }
                }
            }
            lineStr.push(' ">');

            if (typeof this.fillStyle == "object") {
                var focus = {x: "50%", y: "50%"};
                var width = (max.x - min.x);
                var height = (max.y - min.y);
                var dimension = (width > height) ? width : height;

                focus.x = mr((this.fillStyle.focus_.x / width) * 100 + 50) + "%";
                focus.y = mr((this.fillStyle.focus_.y / height) * 100 + 50) + "%";

                var colors = [];

                // inside radius (%)
                if (this.fillStyle.type_ == "gradientradial") {
                    var inside = (this.fillStyle.radius1_ / dimension * 100);

                    // percentage that outside radius exceeds inside radius
                    var expansion = (this.fillStyle.radius2_ / dimension * 100) - inside;
                } else {
                    var inside = 0;
                    var expansion = 100;
                }

                var insidecolor = {offset: null, color: null};
                var outsidecolor = {offset: null, color: null};

                // We need to sort 'colors' by percentage, from 0 > 100 otherwise ie
                // won't interpret it correctly
                this.fillStyle.colors_.sort(function (cs1, cs2) {
                    return cs1.offset - cs2.offset;
                });

                for (var i = 0; i < this.fillStyle.colors_.length; i++) {
                    var fs = this.fillStyle.colors_[i];

                    colors.push( (fs.offset * expansion) + inside, "% ", fs.color, ",");

                    if (fs.offset > insidecolor.offset || insidecolor.offset == null) {
                        insidecolor.offset = fs.offset;
                        insidecolor.color = fs.color;
                    }

                    if (fs.offset < outsidecolor.offset || outsidecolor.offset == null) {
                        outsidecolor.offset = fs.offset;
                        outsidecolor.color = fs.color;
                    }
                }
                colors.pop();

                lineStr.push('<g_vml_:fill',
                    ' color="', outsidecolor.color, '"',
                    ' color2="', insidecolor.color, '"',
                    ' type="', this.fillStyle.type_, '"',
                    ' focusposition="', focus.x, ', ', focus.y, '"',
                    ' colors="', colors.join(""), '"',
                    ' opacity="', opacity, '" />');
            } else if (aFill) {
                lineStr.push('<g_vml_:fill color="', color, '" opacity="', opacity, '" />');
            } else {
                lineStr.push(
                    '<g_vml_:stroke',
                    ' opacity="', opacity,'"',
                    ' joinstyle="', this.lineJoin, '"',
                    ' miterlimit="', this.miterLimit, '"',
                    ' endcap="', processLineCap(this.lineCap) ,'"',
                    ' weight="', this.lineWidth, 'px"',
                    ' color="', color,'" />'
                );
            }

            lineStr.push("</g_vml_:shape>");

            this.element_.insertAdjacentHTML("beforeEnd", lineStr.join(""));

            this.currentPath_ = [];
        };

        contextPrototype.fill = function() {
            this.stroke(true);
        }

        contextPrototype.closePath = function() {
            this.currentPath_.push({type: "close"});
        };

        /**
         * @private
         */
        contextPrototype.getCoords_ = function(aX, aY) {
            return {
                x: Z * (aX * this.m_[0][0] + aY * this.m_[1][0] + this.m_[2][0]) - Z2,
                y: Z * (aX * this.m_[0][1] + aY * this.m_[1][1] + this.m_[2][1]) - Z2
            }
        };

        contextPrototype.save = function() {
            var o = {};
            copyState(this, o);
            this.aStack_.push(o);
            this.mStack_.push(this.m_);
            this.m_ = matrixMultiply(createMatrixIdentity(), this.m_);
        };

        contextPrototype.restore = function() {
            copyState(this.aStack_.pop(), this);
            this.m_ = this.mStack_.pop();
        };

        contextPrototype.translate = function(aX, aY) {
            var m1 = [
                [1,  0,  0],
                [0,  1,  0],
                [aX, aY, 1]
            ];

            this.m_ = matrixMultiply(m1, this.m_);
        };

        contextPrototype.rotate = function(aRot) {
            var c = mc(aRot);
            var s = ms(aRot);

            var m1 = [
                [c,  s, 0],
                [-s, c, 0],
                [0,  0, 1]
            ];

            this.m_ = matrixMultiply(m1, this.m_);
        };

        contextPrototype.scale = function(aX, aY) {
            this.arcScaleX_ *= aX;
            this.arcScaleY_ *= aY;
            var m1 = [
                [aX, 0,  0],
                [0,  aY, 0],
                [0,  0,  1]
            ];

            this.m_ = matrixMultiply(m1, this.m_);
        };

        /******** STUBS ********/
        contextPrototype.clip = function() {
            // TODO: Implement
        };

        contextPrototype.arcTo = function() {
            // TODO: Implement
        };

        contextPrototype.createPattern = function() {
            return new CanvasPattern_;
        };

        // Gradient / Pattern Stubs
        function CanvasGradient_(aType) {
            this.type_ = aType;
            this.radius1_ = 0;
            this.radius2_ = 0;
            this.colors_ = [];
            this.focus_ = {x: 0, y: 0};
        }

        CanvasGradient_.prototype.addColorStop = function(aOffset, aColor) {
            aColor = processStyle(aColor);
            this.colors_.push({offset: 1-aOffset, color: aColor});
        };

        function CanvasPattern_() {}

        // set up externs
        G_vmlCanvasManager = G_vmlCanvasManager_;
        CanvasRenderingContext2D = CanvasRenderingContext2D_;
        CanvasGradient = CanvasGradient_;
        CanvasPattern = CanvasPattern_;

    })();

} // if