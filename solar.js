// Number of days in month, nm
var nm = [31,28,31,30,31,30,31,31,30,31,30,31];

// Table U3: Mean global solar irradiance (W/m2) on a horizontal plane, and solar declination
// Row corresponds to region id in SAP specification 0:UK average etc..
// 2nd dimention row index corresponds to month
var table_u3 = [
[26,54,94,150,190,201,194,164,116,68,33,21],
[29,55,99,153,192,214,204,177,124,73,39,23],
[31,57,103,163,204,225,213,186,129,78,42,24],
[33,59,105,162,207,225,213,190,132,77,43,26],
[33,60,104,166,200,218,208,186,133,75,42,27],
[31,57,100,157,198,218,208,184,128,72,40,24],
[26,53,95,147,188,204,194,168,117,68,34,21],
[23,51,93,144,189,196,186,159,110,63,30,19],
[20,48,87,138,188,192,187,156,110,59,28,17],
[21,47,85,135,182,186,178,149,103,57,28,16],
[22,50,88,139,181,188,183,154,106,61,30,18],
[26,54,94,150,190,201,194,164,116,68,33,21],
[29,56,100,157,196,212,203,173,123,75,38,23],
[26,53,98,152,195,209,198,172,117,67,33,21],
[17,45,84,139,193,186,183,154,102,54,24,13],
[17,45,81,133,185,187,177,146,99,52,24,12],
[17,43,84,131,183,187,170,142,98,51,23,12],
[15,41,82,134,184,181,163,140,97,48,21,11],
[17,38,83,140,200,189,175,147,106,49,21,10],
[16,42,81,144,202,199,178,141,102,49,19,9],
[11,32,72,129,186,183,163,138,87,43,15,6],
[23,49,89,139,190,188,175,152,107,61,29,17]
];

// Index corresponds to month
var solar_declination = [-20.7,-12.8,-1.8,9.8,18.8,23.1,21.2,13.7,2.9,-8.7,-18.4,-23.0];

// Table U4: Representative latitude
// Index corresponds to region id in SAP specification 0:UK average etc..
var table_u4 = [53.4,51.5,51.0,50.8,50.6,51.5,52.7,53.4,54.8,55.5,54.5,53.4,52.3,52.5,55.8,56.4,57.2,57.5,58.0,59.0,60.2,54.7];

// Table U5: Constants for calculation of solar flux on vertical and inclined surfaces
// 2nd dimention index: 0:North 1:NE/NW 2:East/West 3:SE/SW 4:South
var k = [];
k[1] = [0.056,-2.85,-0.241,0.839,2.35];
k[2] = [-5.79,2.89,-0.024,-0.604,-2.97];
k[3] = [6.23,0.298,0.351,0.989,2.4];
k[4] = [3.32,4.52,0.604,-0.554,-3.04];
k[5] = [-0.159,-6.28,-0.494,0.251,3.88];
k[6] = [-3.74,1.47,-0.502,-2.49,-4.97];
k[7] = [-2.7,-2.58,-1.79,-2.0,-1.31];
k[8] = [3.45,3.96,2.06,2.28,1.27];
k[9] = [-1.21,-1.88,-0.405,0.807,1.83];

// U3.2 Solar radiation on vertical and inclined surfaces
function solar_rad(region,orient,p,m)
{
  // convert degrees into radians
  var radians = (p/360.0)*2.0*Math.PI;
 
  var sinp = Math.sin(radians);
  var sin2p = sinp * sinp;
  var sin3p = sinp * sinp * sinp;

  var A = k[1][orient] * sin3p + k[2][orient] * sin2p + k[3][orient] * sinp;
  var B = k[4][orient] * sin3p + k[5][orient] * sin2p + k[6][orient] * sinp;
  var C = k[7][orient] * sin3p + k[8][orient] * sin2p + k[9][orient] * sinp + 1;

  var latitude = (table_u4[region]/360)*2*Math.PI; // get latitude in degrees and convert to radians
  var sol_dec = (solar_declination[m]/360)*2*Math.PI; // get solar_declination in degrees and convert to radians
  var cos1 = Math.cos(latitude - sol_dec);
  var cos2 = cos1 * cos1;

  // Rh-inc(orient, p, m) = A × cos2(φ - δ) + B × cos(φ - δ) + C
  var Rh_inc = A * cos2 + B * cos1 + C;

  return table_u3[region][m] * Rh_inc;
}

// Annual solar radiation on a surface
function annual_solar_rad(region,orient,p)
{
  // month 0 is january, 11: december
  var sum = 0;
  for (var m=0; m<12; m++)
  {
    sum += nm[m] * solar_rad(region,orient,p,m);
  }
  return 0.024 * sum;
}
