/*
 * Copyright (c) 2014 Greg Schoppe <gschoppe@gmail.com>
 * Copyright (c) 2011 Jonathan Perkin <jonathan@perkin.org.uk>
 *
 * Permission to use, copy, modify, and distribute this software for any
 * purpose with or without fee is hereby granted, provided that the above
 * copyright notice and this permission notice appear in all copies.
 *
 * THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES
 * WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR
 * ANY SPECIAL, DIRECT, INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES
 * WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN
 * ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF
 * OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.
 */

/* Parse a ChordPro template */
function parseChordPro(template) {
    var chordregex= /\[([^\]]*)\]/;
    var inword    = /[a-z]$/;
    var buffer    = [];
    var chords    = [];
    if (!template) return;
    
    template.split("\n").forEach(function(line, linenum) {
        /* Comment, ignore */
        if (line.match(/^#/)) {
            return;
        }
        /* Chord line */
        if (line.match(chordregex)) {
            var chords = "";
            var lyrics = "";
            var chordlen = 0;
            line.split(chordregex).forEach(function(word, pos) {
                var dash = 0;
                /* Lyrics */
                if ((pos % 2) == 0) {
                    lyrics = lyrics + word.replace(' ', "&nbsp;");
                  /*
                   * Whether or not to add a dash (within a word)
                   */
                    if (word.match(this.inword)) {
                        dash = 1;
                    }
                  /*
                   * Apply padding.  We never want two chords directly adjacent,
                   * so unconditionally add an extra space.
                   */
                    if (word && word.length < chordlen) {
                        chords = chords + "&nbsp;";
                        lyrics = (dash == 1) ? lyrics + "-&nbsp;" : lyrics + "&nbsp&nbsp;";
                        for (i = chordlen - word.length - dash; i != 0; i--) {
                            lyrics = lyrics + "&nbsp;";
                        }
                    } else if (word && word.length == chordlen) {
                        chords = chords + "&nbsp;";
                        lyrics = (dash == 1) ? lyrics + "-" : lyrics + "&nbsp;";
                    } else if (word && word.length > chordlen) {
                        for (i = word.length - chordlen; i != 0; i--) {
                            chords = chords + "&nbsp;";
                        }
                    }
                } else {
                    /* Chords */
                    chord = word.replace(/[[]]/, "");
                    chordlen = chord.length;
                    chords = chords + "<span class='chord'>" + chord + "</span>";
                }
            }, this);
            buffer.push(chords + "<br/>\n" + lyrics);
            return;
        }
        /* Commands, ignored for now */
        if (line.match(/^{.*}/)) {
            //ADD COMMAND PARSING HERE
            //reference: http://tenbyten.com/software/songsgen/help/HtmlHelp/files_reference.htm
            console.log(line);
            var matches = line.match(/^{(title|t|subtitle|st):(.*)}/, "i");
            // WORK FROM HERE TO IMPLEMENT COMMANDS!
            return;
        }
        /* Anything else */
        buffer.push(line);
    }, this);
    return buffer.join("<br/>\n");
}