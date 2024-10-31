/**
 * QCaptcha is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * QCaptcha is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with QCaptcha. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    QCaptcha
 * @author     Timo Kössler (https://timokoessler.de)
 * @since      1.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2019, Timo Kössler
 */

var p = document.getElementById("qcaptcha_protection");
document.getElementById("qcaptcha_protection_feautures_" + p.value).style.display = "block";

p.addEventListener("input", function() {
    for(var x = 1; x <= 5; x++){
        document.getElementById("qcaptcha_protection_feautures_" + x).style.display = "none";
    }

    document.getElementById("qcaptcha_protection_feautures_" + p.value).style.display = "block";

}, false);