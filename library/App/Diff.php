<?php
/**
 * Books social network
 *
 * LICENSE: Closed source
 *
 * @copyright  2008-2009 Dikun Maxim
 * @version    $Id$
 */
/**
    Diff implemented in pure php, written from scratch.
    Copyright (C) 2003  Daniel Unterberger <diff.phpnet@holomind.de>
    Copyright (C) 2005  Nils Knappmeier next version

    This program is free software; you can redistribute it and/or
    modify it under the terms of the GNU General Public License
    as published by the Free Software Foundation; either version 2
    of the License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.

    http://www.gnu.org/licenses/gpl.html

    About:
    I searched a function to compare arrays and the array_diff()
    was not specific enough. It ignores the order of the array-values.
    So I reimplemented the diff-function which is found on unix-systems
    but this you can use directly in your code and adopt for your needs.
    Simply adopt the formatline-function. with the third-parameter of arr_diff()
    you can hide matching lines. Hope someone has use for this.

    Contact: d.u.diff@holomind.de <daniel unterberger>
 **/
/**
 * Diff utils
 *
 * @author dikmax
 * @version
 */
class App_Diff
{
    const DIFF_NO_CHANGE = 0;
    const DIFF_ADD = 1;
    const DIFF_DELETE = 2;

    /**
     * @param string $old old version of text
     * @param string $new new version of text
     * @return string the differences between $old and $new, formatted
     * in the standard diff(1) output format.
     */
    public static function diff($old, $new)
    {
        // split the source text into arrays of lines
        $t1 = explode("\n", $old);
        $t2 = explode("\n", $new);

        // build a reverse-index array using the line as key and line number as value
        // don't store blank lines, so they won't be targets of the shortest distance
        // search
        foreach ($t1 as $i => $x) {
            if ($x > '') {
                $r1[$x][] = $i;
            }
        }
        foreach ($t2 as $i => $x) {
            if ($x > '') {
                $r2[$x][] = $i;
            }
        }
        $a1 = 0;
        $a2 = 0; // start at beginning of each list
        $actions = array();
        // walk this loop until we reach the end of one of the lists
        while ($a1 < count($t1) && $a2 < count($t2)) {
            // if we have a common element, save it and go to the next
            if ($t1[$a1] == $t2[$a2]) {
                $actions[] = 4;
                $a1 ++;
                $a2 ++;
                continue;
            }
            // otherwise, find the shortest move (Manhattan-distance) from the
            // current location
            $best1 = count($t1);
            $best2 = count($t2);
            $s1 = $a1;
            $s2 = $a2;
            while (($s1 + $s2 - $a1 - $a2) < ($best1 + $best2 - $a1 - $a2)) {
                $d = - 1;
                foreach ((array) @$r1[$t2[$s2]] as $n)
                    if ($n >= $s1) {
                        $d = $n;
                        break;
                    }
                if ($d >= $s1 && ($d + $s2 - $a1 - $a2) < ($best1 + $best2 - $a1 - $a2)) {
                    $best1 = $d;
                    $best2 = $s2;
                }
                $d = - 1;
                foreach ((array) @$r2[$t1[$s1]] as $n)
                    if ($n >= $s2) {
                        $d = $n;
                        break;
                    }
                if ($d >= $s2 && ($s1 + $d - $a1 - $a2) < ($best1 + $best2 - $a1 - $a2)) {
                    $best1 = $s1;
                    $best2 = $d;
                }
                $s1 ++;
                $s2 ++;
            }
            while ($a1 < $best1) {
                $actions[] = 1;
                $a1 ++;
            } // deleted elements
            while ($a2 < $best2) {
                $actions[] = 2;
                $a2 ++;
            } // added elements
        }
        // we've reached the end of one list, now walk to the end of the other
        while ($a1 < count($t1)) {
            $actions[] = 1;
            $a1 ++;
        } // deleted elements
        while ($a2 < count($t2)) {
            $actions[] = 2;
            $a2 ++;
        } // added elements
        // and this marks our ending point
        $actions[] = 8;
        // now, let's follow the path we just took and report the added/deleted
        // elements into $out.

        $op = 0;
        $x0 = $x1 = 0;
        $y0 = $y1 = 0;
        $out = array();
        foreach ($actions as $act) {
            if ($act == 1) {
                $op |= $act;
                $x1 ++;
                continue;
            }
            if ($act == 2) {
                $op |= $act;
                $y1 ++;
                continue;
            }
            if ($op > 0) {
                while ($x0 < $x1) {
                    $out[] = array(
                        'type' => self::DIFF_DELETE,
                        'x' => $x0 + 1,
                        'y' => '',
                        'l' => $t1[$x0]
                    );
                    $x0++;
                } // deleted elems
                while ($y0 < $y1) {
                    $out[] = array(
                        'type' => self::DIFF_ADD,
                        'x' => '',
                        'y' => $y0 + 1,
                        'l' => $t2[$y0]
                    );
                    $y0 ++;
                } // added elems

                if (isset($t1[$x0])) { // Line after changes
                    $out[] = array(
                        'type' => self::DIFF_NO_CHANGE,
                        'x' => $x0 + 1,
                        'y' => $y0 + 1,
                        'l' => $t2[$y0]
                    );
                }
            } else {
                if (isset($t1[$x0])) {
                    $out[] = array(
                        'type' => self::DIFF_NO_CHANGE,
                        'x' => $x0 + 1,
                        'y' => $y0 + 1,
                        'l' => $t2[$y0]
                    );
                }
            }
            $x1++;
            $x0 = $x1;
            $y1++;
            $y0 = $y1;
            $op = 0;
        }
        return $out;
    }
}