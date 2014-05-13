/**
 * Utility functions.
 */

"use strict";

var Util = {
    Image: {
        images: [],
        loadedCount: 0,
        callback: null,

        load: function(path) {
            var image = new Image();
            image.src = path;
            image.onload = this.onload.bind(this);

            this.images.push(image);

            return image;
        },

        onload: function() {
            // when everything is loaded, we call the callback, if it's set
            if (this.images.length === ++this.loadedCount) {
                this.callback && this.callback();
            }
        }
    }
};
