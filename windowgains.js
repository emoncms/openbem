
function calc_solar_gains_from_windows(windows,region)
{
        var gains = [0,0,0,0,0,0,0,0,0,0,0,0];

        for (z in windows)
        {
          var orientation = windows[z]['orientation'];
          var area = windows[z]['area'];
          var overshading = windows[z]['overshading'];
          var g = windows[z]['g'];
          var ff = windows[z]['ff'];

          // The gains for a given window are calculated for each month
          // the result of which needs to be put in a bin for totals for jan, feb etc..
          for (var month=0; month<12; month++)
          {
            // Access factor table: first dimention is shading factor, 2nd in winter, summer.
            var table_6d = [[0.3,0.5],[0.54,0.7],[0.77,0.9],[1.0,1.0]];
         
            // access factor is time of year dependent
            // Summer months: 5:June, 6:July, 7:August and 8:September (where jan = month 0)
            var summer = 0; if (month>=5 && month<=8) summer = 1;
            var access_factor = table_6d[overshading][summer];

            // Map orientation code from window to solar rad orientation codes.
            if (orientation == 5) orientation = 3; // SE/SW
            if (orientation == 6) orientation = 2; // East/West
            if (orientation == 7) orientation = 1; // NE/NW

            gains[month] += access_factor * area * solar_rad(region,orientation,90,month) * 0.9 * g * ff;
          }
        }
  return gains;
}
