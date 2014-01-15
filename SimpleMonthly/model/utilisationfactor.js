
// Calculation of mean internal temperature for heating
// Calculation of mean internal temperature is based on the heating patterns defined in Table 9.

function calc_utilisation_factor(TMP,HLP,H,Ti,Te,G)
{
  /* 
    Symbols and units
    H = heat transfer coefficient, (39)m (W/K)
    G = total gains, (84)m (W)
    Ti = internal temperature (°C)
    Te = external temperature, (96)m (°C)
    TMP = Thermal Mass Parameter, (35), (kJ/m2K) (= Cm for building / total floor area)
    HLP = Heat Loss Parameter, (40)m (W/m2K)
    τ = time constant (h)
    η = utilisation factor
    L = heat loss rate (W)
  */

  // Calculation of utilisation factor

  // TMP = thermal Mass / Total floor area
  // HLP = heat transfer coefficient (H) / Total floor area

  var tau = TMP / (3.6 * HLP);
  var a = 1.0 + tau / 15.0;

  // calc losses
  var L = H * (Ti - Te);

  // ratio of gains to losses
  var y = G / L; 

  // Note: to avoid instability when γ is close to 1 round γ to 8 decimal places
  // y = y.toFixed(8);
  y = Math.round(y*100000000.0) / 100000000.0;

  var n = 0.0;
  if (y>0.0 && y!=1.0) n = (1.0 - Math.pow(y,a)) / (1.0 - Math.pow(y,a+1.0));
  if (y == 1.0) n = a / (a + 1.0);

  return n;
}

function calc_temperature_reduction(TMP,HLP,H,Ti,Te,G, R,Th,toff)
{
  // Calculation of utilisation factor
  var tau = TMP / (3.6 * HLP);
  var a = 1.0 + tau / 15.0;
  var L = H * (Ti - Te);
  var y = G / L;

  // Note: to avoid instability when γ is close to 1 round γ to 8 decimal places
  // y = y.toFixed(8);
  y = Math.round(y*100000000.0) / 100000000.0;
  var n = 0.0;
  if (y>0.0 && y!=1.0) n = (1.0 - Math.pow(y,a)) / (1.0 - Math.pow(y,a+1.0));
  if (y == 1.0) n = a / (a + 1.0);

  var tc = 4.0 + 0.25 * tau;

  var Tsc = (1.0 - R) * (Th - 2.0) + R * (Te + n * G / H);

  var u;
  if (toff <= tc) u = 0.5 * toff * toff * (Th - Tsc) / (24 * tc);
  if (toff > tc) u = (Th - Tsc) * (toff - 0.5 * tc) / 24;

  //console.log(Tsc);

  return u;
}

