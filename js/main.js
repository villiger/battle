/**
 * Entry point for custom JavaScript code.
 */

Game = {
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
        this.context.imageSmoothingEnabled = false;

        this.Field.init();
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
            var that = this;

            this.images.ground = new Image();
            this.images.ground.src = '/img/field/grass.png';
            this.images.ground.onload = function() {
                that.draw();
            };

            this.images.tiles = new Image();
            this.images.tiles.src = '/img/field/tiles.png';
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

            Game.context.drawImage(this.images.ground, 0, 0, ss, ss, dx, dy, ds, ds);
            Game.context.drawImage(this.images.tiles, sx, sy, ss, ss, dx, dy, ds, ds);
        }
    }
};
