/**
 * PrayTimes.js - Library Perhitungan Waktu Sholat
 * Berdasarkan praytimes.org oleh Hamid Zarrabi-Zadeh
 * Dimodifikasi untuk Display Masjid KUA Seri Kuala Lobam
 *
 * Metode yang didukung:
 * - MWL: Muslim World League
 * - ISNA: Islamic Society of North America
 * - Egypt: Egyptian General Authority of Survey
 * - Makkah: Umm al-Qura University
 * - Karachi: University of Islamic Sciences
 * - Tehran: Institute of Geophysics
 * - Jafari: Shia Ithna Ashari
 * - Kemenag: Kementerian Agama RI (Manual)
 */
var PrayTimes = (function () {
    'use strict';

    // Metode perhitungan
    var methods = {
        MWL: { name: 'Muslim World League', params: { fajr: 18, isha: 17 } },
        ISNA: { name: 'Islamic Society of North America', params: { fajr: 15, isha: 15 } },
        Egypt: { name: 'Egyptian General Authority', params: { fajr: 19.5, isha: 17.5 } },
        Makkah: { name: 'Umm Al-Qura University, Makkah', params: { fajr: 18.5, isha: '90 min' } },
        Karachi: { name: 'University of Islamic Sciences', params: { fajr: 18, isha: 18 } },
        Tehran: { name: 'Institute of Geophysics, Tehran', params: { fajr: 17.7, isha: 14, maghrib: 4.5, midnight: 'Jafari' } },
        Jafari: { name: 'Shia Ithna-Ashari, Leva Institute', params: { fajr: 16, isha: 14, maghrib: 4, midnight: 'Jafari' } },
        Kemenag: { name: 'Kementerian Agama RI', params: { fajr: 20, isha: 18 } }
    };

    // Default settings
    var setting = {
        imsak: '10 min',
        dhuhr: '0 min',
        asr: 'Standard',
        highLats: 'NightMiddle'
    };

    var calcMethod = 'Kemenag';
    var lat, lng, elv, timeZone, jDate;

    // Fungsi trigonometri dalam derajat
    function dtr(d) { return (d * Math.PI) / 180.0; }
    function rtd(r) { return (r * 180.0) / Math.PI; }
    function dsin(d) { return Math.sin(dtr(d)); }
    function dcos(d) { return Math.cos(dtr(d)); }
    function dtan(d) { return Math.tan(dtr(d)); }
    function darcsin(x) { return rtd(Math.asin(x)); }
    function darccos(x) { return rtd(Math.acos(x)); }
    function darctan2(y, x) { return rtd(Math.atan2(y, x)); }
    function darccot(x) { return rtd(Math.atan(1 / x)); }
    function fixAngle(a) { return fix(a, 360); }
    function fixHour(a) { return fix(a, 24); }
    function fix(a, b) { a = a - b * Math.floor(a / b); return (a < 0) ? a + b : a; }

    // Posisi matahari
    function sunPosition(jd) {
        var D = jd - 2451545.0;
        var g = fixAngle(357.529 + 0.98560028 * D);
        var q = fixAngle(280.459 + 0.98564736 * D);
        var L = fixAngle(q + 1.915 * dsin(g) + 0.020 * dsin(2 * g));
        var e = 23.439 - 0.00000036 * D;
        var RA = darctan2(dcos(e) * dsin(L), dcos(L)) / 15;
        var eqt = q / 15 - fixHour(RA);
        var decl = darcsin(dsin(e) * dsin(L));
        return { declination: decl, equation: eqt };
    }

    // Julian Date
    function julian(year, month, day) {
        if (month <= 2) { year -= 1; month += 12; }
        var A = Math.floor(year / 100);
        var B = 2 - A + Math.floor(A / 4);
        return Math.floor(365.25 * (year + 4716)) + Math.floor(30.6001 * (month + 1)) + day + B - 1524.5;
    }

    // Waktu tengah hari (transit matahari)
    function midDay(t) {
        var eqt = sunPosition(jDate + t).equation;
        return fixHour(12 - eqt);
    }

    // Sudut waktu matahari
    function sunAngleTime(angle, time, direction) {
        var decl = sunPosition(jDate + time).declination;
        var noon = midDay(time);
        var t = 1 / 15 * darccos((-dsin(angle) - dsin(decl) * dsin(lat)) / (dcos(decl) * dcos(lat)));
        return noon + (direction === 'ccw' ? -t : t);
    }

    // Waktu Ashar (Hanafi atau Standard)
    function asrTime(factor, time) {
        var decl = sunPosition(jDate + time).declination;
        var angle = -darccot(factor + dtan(Math.abs(lat - decl)));
        return sunAngleTime(angle, time);
    }

    // Hitung waktu sholat untuk satu hari
    function computeTimes() {
        var times = { imsak: 5, fajr: 5, sunrise: 6, dhuhr: 12, asr: 13, sunset: 18, maghrib: 18, isha: 18 };
        var params = methods[calcMethod].params;

        // Iterasi untuk akurasi
        for (var i = 1; i <= 2; i++) {
            times.imsak = sunAngleTime(eval2(setting.imsak, params.fajr || 10), times.imsak, 'ccw');
            times.fajr = sunAngleTime(params.fajr, times.fajr, 'ccw');
            times.sunrise = sunAngleTime(riseSetAngle(), times.sunrise, 'ccw');
            times.dhuhr = midDay(times.dhuhr);
            times.asr = asrTime(asrFactor(), times.asr);
            times.sunset = sunAngleTime(riseSetAngle(), times.sunset);
            times.maghrib = sunAngleTime(eval2(params.maghrib || 0, 0), times.maghrib);
            times.isha = sunAngleTime(eval2(params.isha, 0), times.isha);
        }
        return times;
    }

    function eval2(val, defVal) {
        if (typeof val === 'number') return val;
        if (typeof val === 'string' && val.indexOf('min') !== -1) {
            // Jika defVal disediakan (konteks computeTimes), gunakan defVal sebagai sudut
            // Jika tidak (konteks adjustTimes), parse nilai menit dari string
            if (typeof defVal !== 'undefined') return defVal;
            return parseFloat(val) || 0;
        }
        return parseFloat(val) || defVal || 0;
    }

    function riseSetAngle() {
        var angle = 0.0347 * Math.sqrt(elv || 0);
        return 0.833 + angle;
    }

    function asrFactor() {
        return setting.asr === 'Hanafi' ? 2 : 1;
    }

    // Terapkan offset (koreksi waktu)
    function tuneTimes(times) {
        for (var i in times) {
            if (typeof times[i] === 'number') {
                times[i] += (offsets[i] || 0) / 60;
            }
        }
        return times;
    }

    // Sesuaikan berdasarkan timezone
    function adjustTimes(times) {
        for (var i in times) {
            if (typeof times[i] === 'number') {
                times[i] += timeZone - lng / 15;
            }
        }
        // Imsak
        if (isMin(setting.imsak)) {
            times.imsak = times.fajr - eval2(setting.imsak) / 60;
        }
        // Maghrib
        var params = methods[calcMethod].params;
        if (isMin(params.maghrib)) {
            times.maghrib = times.sunset + eval2(params.maghrib) / 60;
        }
        // Isha
        if (isMin(params.isha)) {
            times.isha = times.maghrib + eval2(params.isha) / 60;
        }
        // Dhuhr
        if (isMin(setting.dhuhr)) {
            times.dhuhr += eval2(setting.dhuhr) / 60;
        }
        return times;
    }

    function isMin(val) {
        return typeof val === 'string' && val.indexOf('min') !== -1;
    }

    // Format waktu ke HH:MM
    function formatTime(time) {
        if (isNaN(time)) return '--:--';
        time = fixHour(time + 0.5 / 60); // pembulatan
        var hours = Math.floor(time);
        var minutes = Math.floor((time - hours) * 60);
        return twoDigits(hours) + ':' + twoDigits(minutes);
    }

    function twoDigits(num) {
        return (num < 10 ? '0' : '') + num;
    }

    var offsets = {};

    // Public API
    return {
        /**
         * Set metode perhitungan
         * @param {string} method - Nama metode (MWL, ISNA, Egypt, Makkah, Karachi, Tehran, Jafari, Kemenag)
         */
        setMethod: function (method) {
            if (methods[method]) {
                calcMethod = method;
            }
        },

        /**
         * Set koreksi waktu (offset dalam menit)
         * @param {object} offsets - { fajr, dhuhr, asr, maghrib, isha }
         */
        tune: function (timeOffsets) {
            offsets = timeOffsets || {};
        },

        /**
         * Hitung jadwal sholat
         * @param {Date|Array} date - Tanggal (Date object atau [year, month, day])
         * @param {Array} coords - [latitude, longitude, elevation]
         * @param {number} timezone - Offset timezone (jam)
         * @returns {object} Waktu sholat { imsak, fajr, sunrise, dhuhr, asr, sunset, maghrib, isha }
         */
        getTimes: function (date, coords, timezone) {
            lat = coords[0];
            lng = coords[1];
            elv = coords[2] || 0;
            timeZone = timezone || this.detectTimezone(date);

            if (date instanceof Date) {
                jDate = julian(date.getFullYear(), date.getMonth() + 1, date.getDate()) - lng / (15 * 24);
            } else {
                jDate = julian(date[0], date[1], date[2]) - lng / (15 * 24);
            }

            var times = computeTimes();
            times = adjustTimes(times);
            times = tuneTimes(times);

            // Format ke string HH:MM
            var result = {};
            for (var i in times) {
                result[i] = formatTime(times[i]);
            }
            return result;
        },

        /**
         * Deteksi timezone otomatis
         */
        detectTimezone: function (date) {
            var d = date instanceof Date ? date : new Date();
            return -d.getTimezoneOffset() / 60;
        },

        /**
         * Daftar metode yang tersedia
         */
        getMethods: function () {
            return methods;
        }
    };
})();
