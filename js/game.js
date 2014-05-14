/**
 * Game functions.
 */

"use strict";

var Game = {
    selection: null,
    images: {},

    init: function(state) {
        this.Field.width = state.field.width;
        this.Field.height = state.field.height;
        this.Field.tiles = state.field.tiles;

        Util.Canvas.init('field', this.Field.width, this.Field.height);

        Util.Image.onloaded = this.draw.bind(this);
        Util.Canvas.onselect = this.onselect.bind(this);

        this.images.selection = Util.Image.load('/img/selection.png');

        this.Field.init();
    },

    draw: function() {
        this.Field.draw();

        // if we have a selection, we draw the selection image
        if (this.selection) {
            Util.Canvas.drawTile(this.images.selection, 0, 0, this.selection.row, this.selection.column);
        }
    },

    onselect: function(row, column) {
        var selection = {
            row: row,
            column: column
        };

        // we compare the current selection and the new selection.
        // if it's the same thing, we remove the selection.
        if (JSON.stringify(this.selection) === JSON.stringify(selection)) {
            this.selection = null;
        } else {
            this.selection = selection;
        }

        // re-draw the game after selection
        this.draw();
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
            Util.Canvas.drawTile(this.images.grass, 0, 0, row, column);

            if (type > 0) {
                Util.Canvas.drawTile(this.images.tiles, 0, type, row, column);
            }
        }
    }
};
