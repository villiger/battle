/**
 * Game functions.
 */

"use strict";

var Game = {
    id: null,
    userId: null,
    selection: null,
    images: {},
    currentPlayer: null,
    player: null,
    opponent: null,
    players: null,

    init: function(userId, state) {
        this.id = state.id;
        this.userId = userId;
        this.players = state.players;
        this.currentPlayer = this.players[state.current_player_id];

        for (var playerId in this.players) {
            var player = this.players[playerId];
            if (playerId == this.userId) {
                this.player = player;
            } else {
                this.opponent = player;
            }
        }

        this.Field.width = state.field.width;
        this.Field.height = state.field.height;
        this.Field.tiles = state.field.tiles;
        this.Field.units = state.field.units;

        this.Action.lastActionId = state.last_action_id;

        Util.Canvas.init('field', this.Field.width, this.Field.height);

        Util.Image.onloaded = this.draw.bind(this);
        Util.Canvas.onselect = this.onselect.bind(this);

        this.images.selection = Util.Image.load('/img/selection.png');

        this.Field.init();
        this.Action.init();

        for (var i = 0; i < state.messages.length; i++) {
            var message = state.messages[i];
            var user = this.players[message.user_id];
            this.Action.Appliers.message(user, message.message);
        }

        Util.Misc.checkEndTurnButton();
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
            this.Field.selectedUnit = null;
        } else {
            this.selection = selection;

            var unit = this.Field.getUnitByPosition(row, column);
            if (unit) {
                if (this.Field.selectedUnit && unit.user_id != this.userId) {
                    this.Action.Creators.attack(this.Field.selectedUnit, unit);

                    this.Field.selectedUnit = null;
                } else {
                    this.Field.selectedUnit = unit;
                }
            } else {
                if (this.Field.selectedUnit) {
                    this.Action.Creators.move(this.Field.selectedUnit, row, column);
                } else {
                    this.Field.selectedUnit = null;
                }
            }
        }

        // re-draw the game after selection
        this.draw();
    },

    isCurrentPlayer: function() {
        return this.currentPlayer.id == this.player.id;
    },

    Field: {
        width: null,
        height: null,
        tiles: null,
        units: null,
        images: {},
        selectedUnit: null,

        TILE_GROUND: 0,
        TILE_FOREST: 1,
        TILE_WATER: 2,
        TILE_MOUNTAIN: 3,
        TILE_DESERT: 4,

        init: function() {
            this.images.grass = Util.Image.load('/img/field/grass.png');
            this.images.tiles = Util.Image.load('/img/field/tiles.png');
            this.images.units = Util.Image.load('/img/field/units.png');
        },

        draw: function() {
            // draw tiles
            for (var row = 0; row < this.height; row++) {
                for (var column = 0; column < this.width; column++) {
                    this.drawTile(this.tiles[row][column], row, column);
                }
            }

            // draw units
            for (var unitId in this.units) {
                var unit = this.units[unitId];

                this.drawUnit(unit);
            }
        },

        drawTile: function(type, row, column) {
            Util.Canvas.drawTile(this.images.grass, 0, 0, row, column);

            if (type > 0) {
                Util.Canvas.drawTile(this.images.tiles, 0, type, row, column);
            }
        },

        drawUnit: function(unit) {
            var type = unit.user_id == Game.userId ? 0 : 1;
            Util.Canvas.drawTile(this.images.units, 0, type, unit.row, unit.column);

            var context = Util.Canvas.context;
            var tileSize = Util.Canvas.tileSize;

            context.fillStyle = "rgb(0, 0, 0)";
            context.fillRect(
                tileSize * unit.column + 15,
                tileSize * unit.row + (tileSize - 10),
                tileSize - 30,
                5
            );

            context.fillStyle = "rgb(0, 255, 0)";
            context.fillRect(
                tileSize * unit.column + 16,
                tileSize * unit.row + (tileSize - 9),
                (tileSize - 32) * unit.life / unit.max_life,
                3
            );

            if (Game.currentPlayer.id == unit.user_id) {
                context.fillStyle = "rgb(255, 255, 255)";
                context.font = "12px sans-serif";
                context.fillText(
                    unit.energy,
                    tileSize * unit.column + tileSize - 21,
                    tileSize * unit.row + tileSize - 12
                );
            }
        },

        replenishUnits: function() {
            for (var unitId in this.units) {
                var unit = this.units[unitId];
                unit.energy = unit.max_energy;
            }
        },

        getUnitById: function(id) {
            return this.units.hasOwnProperty(id) ? this.units[id] : null;
        },

        getUnitByPosition: function(row, column) {
            for (var unitId in this.units) {
                var unit = this.units[unitId];
                if (unit.row == row && unit.column == column) {
                    return unit;
                }
            }

            return null;
        }
    },

    Action: {
        lastActionId: null,

        init: function() {
            setInterval(this.update.bind(this), 3000);
        },

        update: function() {
            $.getJSON('/game/' + Game.id + '/action/' + this.lastActionId, function(result) {
                Game.Action.lastActionId = result.last_action_id;

                $.each(result.actions, function (index, action) {
                    var user = Game.players[action.user_id];

                    // don't apply my own actions again
                    if (user.id != Game.userId) {
                        switch (action.type) {
                            case 'message':
                                Game.Action.Appliers.message(user, action.payload.message);
                                break;

                            case 'move':
                                var movingUnit = Game.Field.getUnitById(action.payload.unit_id);
                                Game.Action.Appliers.move(movingUnit, action.payload.row, action.payload.column);
                                break;

                            case 'attack':
                                var attackerUnit = Game.Field.getUnitById(action.payload.unit_id);
                                var damage = action.payload.damage;
                                Game.Action.Appliers.attack(attackerUnit, action.payload.row, action.payload.column, damage);
                                break;

                            case 'end_turn':
                                Game.Action.Appliers.endTurn(user);
                                break;
                        }
                    }
                });
            });
        },

        Creators: {
            message: function(message) {
                $.post('/game/' + Game.id + '/action/message', {
                    payload: {
                        message: message
                    }
                }, function () {
                    Game.Action.Appliers.message(Game.player, message);
                }.bind(this));
            },

            move: function(unit, row, column) {
                if (! Game.isCurrentPlayer()) return;
                if (Game.userId != unit.user_id) return;

                $.post('/game/' + Game.id + '/action/move', {
                    payload: {
                        unit_id: unit.id,
                        row: row,
                        column: column
                    }
                }, function () {
                    Game.Action.Appliers.move(unit, row, column);
                });
            },

            attack: function(unit, target) {
                if (! Game.isCurrentPlayer()) return;
                if (Game.userId != unit.user_id) return;
                if (unit.user_id == target.user_id) return;

                $.post('/game/' + Game.id + '/action/attack', {
                    payload: {
                        unit_id: unit.id,
                        row: target.row,
                        column: target.column
                    }
                }, function (result) {
                    Game.Action.Appliers.attack(unit, target.row, target.column, result.payload.damage);
                });
            },

            endTurn: function() {
                if (! Game.isCurrentPlayer()) return;

                $.post('/game/' + Game.id + '/action/end_turn', function () {
                    Game.Action.Appliers.endTurn(Game.player);
                });
            }
        },

        Appliers: {
            message: function(user, message) {
                $("#messages").prepend("<div>" + user.name + ": " + message + "</div>");
            },

            move: function(unit, row, column) {
                unit.row = row;
                unit.column = column;

                Game.draw();
            },

            attack: function(unit, row, column, damage) {
                var target = Game.Field.getUnitByPosition(row, column);
                if (target) {
                    target.life = target.life - damage;
                    if (target.life <= 0) {
                        delete Game.Field.units[target.id];

                        // check if the player from who a unit has been snatched
                        // has any other units on the battlefield
                        var unitsLeft = false;
                        for (var unitId in Game.Field.units) {
                            var unit = Game.Field.units[unitId];

                            if (unit.user_id != Game.currentPlayer.id) {
                                unitsLeft = true;
                            }
                        }

                        // if not, he has lost the game
                        if (! unitsLeft) {
                            $('#end-game-title').html(Game.currentPlayer.name + " has won the game!");

                            $('#end-game-modal').modal().on('hidden.bs.modal', function () {
                                window.location = '/games';
                            });
                        }
                    }

                    Game.draw();
                }
            },

            endTurn: function(user) {
                Game.currentPlayer = user.id == Game.player.id ? Game.opponent : Game.player;
                Game.Field.replenishUnits();
                Game.draw();

                Util.Misc.checkEndTurnButton();
            }
        }
    }
};
