/**
 * Game functions.
 */

"use strict";

var Game = {
    tileBaseSize: 16,
    tileScale: 5,
    tileSize: null,
    canvas: null,
    context: null,

    init: function(state) {
        this.tileSize = this.tileBaseSize * this.tileScale;

        this.Field.width = state.field.width;
        this.Field.height = state.field.height;
        this.Field.tiles = state.field.tiles;

        this.canvas = document.getElementById('field');
        this.canvas.width = this.Field.width * this.tileSize;
        this.canvas.height = this.Field.height * this.tileSize;

        this.context = this.canvas.getContext('2d');

        // disable image smoothing on scale, we are a pixel art game
        this.context.mozImageSmoothingEnabled = false;
        this.context.webkitImageSmoothingEnabled = false;
        this.context.msImageSmoothingEnabled = false;
        this.context.imageSmoothingEnabled = false;

        // draw the game if all images are loaded
        Util.Image.callback = this.draw.bind(this);

        this.Field.init();
    },

    draw: function() {
        this.Field.draw();
    },

    Field: {
        width: null,
        height: null,
        tiles: null,
        images: {},

        TILE_GROUND: 0,
        TILE_FOREST: 1,
        TILE_WATER: 2,
        TILE_MOUNTAIN: 3,
        TILE_DESERT: 4,

        init: function() {
            this.images.grass = Util.Image.load('/img/field/grass.png');
            this.images.tiles = Util.Image.load('/img/field/tiles.png');
        },

        draw: function() {
            for (var row = 0; row < this.height; row++) {
                for (var column = 0; column < this.width; column++) {
                    this.drawTile(this.tiles[row][column], row, column);
                }
            }
        },

        drawTile: function(type, row, column) {
            // source coordinates
            var ss = Game.tileBaseSize;
            var sx = ss * type;
            var sy = 0;

            // destination coordinates
            var ds = Game.tileSize;
            var dx = ds * column;
            var dy = ds * row;

            // draw the first layer, which is the ground
            Game.context.drawImage(this.images.grass, 0, 0, ss, ss, dx, dy, ds, ds);

            // only draw the second tile layer, if it's not ground
            if (type > 0) {
                Game.context.drawImage(this.images.tiles, sx, sy, ss, ss, dx, dy, ds, ds);
            }
        }
    }
};
