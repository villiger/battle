/**
 * Pathfinding class.
 */

"use strict";

function Path(fromRow, fromColumn, toRow, toColumn, ignoreObstacles) {
    this.fromRow = fromRow;
    this.fromColumn = fromColumn;
    this.toRow = toRow;
    this.toColumn = toColumn;
    this.field = {};
    this.path = {};

    for (var row = 0; row < Game.Field.height; row++) {
        for (var column = 0; column < Game.Field.width; column++) {
            if (! this.field[row]) {
                this.field[row] = {};
            }

            if (! this.path[row]) {
                this.path[row] = {};
            }

            if (row == fromRow && column == fromColumn) {
                this.field[row][column] = "from";
            } else {
                this.field[row][column] = ignoreObstacles ? Game.Field.INFO_FREE : Game.Field.getTileInfo(row, column);
            }

            this.path[row][column] = -1;
        }
    }
}

Path.prototype.calculate = function(maxRange) {
    // no path if start and end position is the same
    if (this.fromRow == this.toRow && this.fromColumn == this.toColumn) {
        return [];
    }

    // no path if click on "my" units
    if (this.field[this.toRow][this.toColumn] == "my") {
        return [];
    }

    this._createPathMap(maxRange);

    return this._findPath(maxRange);
};

Path.prototype._tryMarkTile = function(pointsArray, row, column, counter) {
    if (row < 0 || row >= Game.Field.height) {
        return false;
    } else if (column < 0 || column >= Game.Field.width) {
        return false;
    } else if (this.field[row][column] == Game.Field.INFO_FREE && this.path[row][column] < 0) {
        this.path[row][column] = counter;

        pointsArray.push({
            row: row,
            column: column
        });

        return false;
    }

    return false;
};

Path.prototype._getNumberFromPath = function(row, column) {
    if (row < 0 || row >= Game.Field.height) {
        return Infinity;
    } else if (column < 0 || column >= Game.Field.width) {
        return Infinity;
    } else if (this.path[row][column] < 0) {
        return Infinity;
    } else {
        return this.path[row][column];
    }
};

Path.prototype._createPathMap = function(maxRange) {
    var counter = 0;
    var lastPoints = [{
        row: this.toRow,
        column: this.toColumn
    }];

    this.path[this.toRow][this.toColumn] = 0;

    while (counter < maxRange) {
        counter++;
        var currentPoints = [];

        for (var i in lastPoints) {
            var point = lastPoints[i];
            var row = point.row;
            var column = point.column;

            this._tryMarkTile(currentPoints, row - 1, column, counter);
            this._tryMarkTile(currentPoints, row + 1, column, counter);
            this._tryMarkTile(currentPoints, row, column - 1, counter);
            this._tryMarkTile(currentPoints, row, column + 1, counter);
        }

        lastPoints = currentPoints;
    }
};

Path.prototype._findPath = function(maxRange) {
    var result = [];
    var currentPosition = {
        row: this.fromRow,
        column: this.fromColumn
    };
    var checkThat = [
        { row: -1, column: 0 },
        { row: +1, column: 0 },
        { row: 0, column: -1 },
        { row: 0, column: +1 }
    ];

    for (var step = 0; step < maxRange; step++) {
        var lowest = Infinity;
        var lowestPos = null;

        for (var j = 0; j < checkThat.length; j++) {
            var current = this._getNumberFromPath(currentPosition.row + checkThat[j].row, currentPosition.column + checkThat[j].column);
            if (current < lowest) {
                lowest = current;
                lowestPos = {
                    row: currentPosition.row + checkThat[j].row,
                    column: currentPosition.column + checkThat[j].column
                };
            }
        }

        if (lowestPos) {
            currentPosition = lowestPos;

            var fieldInfo = this.field[currentPosition.row][currentPosition.column];
            if (fieldInfo == Game.Field.INFO_FREE) {
                result.push(lowestPos);
            }
        }


        if (lowest == 0) return result;
    }

    return [];
};