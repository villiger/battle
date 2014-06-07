/**
 * Utility functions.
 */

"use strict";

var Util = {
    /**
     * The Image object loads images and calls the callback, if all
     * images are done loading.
     */
    Image: {
        images: [],
        loadedCount: 0,
        onloaded: null,

        load: function(path) {
            var image = new Image();
            image.src = path;
            image.onload = this._onload.bind(this);

            this.images.push(image);

            return image;
        },

        _onload: function() {
            // when everything is loaded, we call the callback, if it's set
            if (this.images.length === ++this.loadedCount) {
                this.onloaded && this.onloaded();
            }
        }
    },

    Canvas: {
        canvas: null,
        context: null,
        onselect: null,

        tileBaseSize: 16,
        tileScale: 5,
        tileSize: null,

        init: function(canvasId, columns, rows) {
            this.tileSize = this.tileBaseSize * this.tileScale;

            this.canvas = document.getElementById(canvasId);
            this.canvas.width = columns * this.tileSize;
            this.canvas.height = rows * this.tileSize;

            // listen to click/touch events
            this.canvas.addEventListener('mousedown', this._onmousedown.bind(this), false);
            this.canvas.addEventListener('touchstart', this._ontouchstart.bind(this), false);

            this.context = this.canvas.getContext('2d');

            // disable image smoothing on scale, we are a pixel art game
            this.context.mozImageSmoothingEnabled = false;
            this.context.webkitImageSmoothingEnabled = false;
            this.context.msImageSmoothingEnabled = false;
            this.context.imageSmoothingEnabled = false;
        },

        drawTile: function(image, srcRow, srcColumn, destRow, destColumn) {
            // source pixel coordinates
            var ss = this.tileBaseSize;
            var sx = ss * srcColumn;
            var sy = ss * srcRow;

            // destination pixel coordinates
            var ds = this.tileSize;
            var dx = ds * destColumn;
            var dy = ds * destRow;

            this.context.drawImage(image, sx, sy, ss, ss, dx, dy, ds, ds);
        },

        _onmousedown: function(event) {
            event.preventDefault();

            this._onclick(event.clientX, event.clientY);
        },

        _ontouchstart: function(event) {
            event.preventDefault();

            this._onclick(event.targetTouches[0].clientX, event.targetTouches[0].clientY);
        },

        _onclick: function(clientX, clientY) {
            // get relative coordinates to canvas origin
            var rect = this.canvas.getBoundingClientRect();
            var x = clientX - rect.left;
            var y = clientY - rect.top;

            // calculate which row and column got clicked
            var row = Math.floor(y / this.tileSize);
            var column = Math.floor(x / this.tileSize);

            this.onselect && this.onselect(row, column);
        }
    },

    Misc: {
        checkEndTurnButton : function() {
            if (Game.isCurrentPlayer()) {
                $('#end-turn').attr('disabled', null).val('End turn');
            } else {
                $('#end-turn').attr('disabled', 'disabled').val('Waiting for other player...');
            }
        }
    }
};
