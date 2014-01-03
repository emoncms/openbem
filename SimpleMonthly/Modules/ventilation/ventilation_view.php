
<table class="table">

<tr><th>Air tightness test:</th><th style="width:70%"></th></tr>
<tr><td>Calculate infiltration rate based on air tightness test:</td><td><input class="ventilation" name="air_permeability_test" type="checkbox" /></td></tr>
<tbody id="air_permeability_value">
<tr><td>Air permeability value, q50, expressed in cubic metres per hour per square metre of envelope area:</td><td><input class="ventilation" name="air_permeability_value" type="text" style="width:50px"/></td></tr>
</tbody>
</table>

<table class="table">
  <tbody>
  <tr><th>Enter number of:</th><th></th><th></th><th></th><th></th><th></th></tr>
  </tbody>
  <tr>
    <td>Chimneys</td><td><input class="ventilation" name="number_of_chimneys" type="text" style="width:50px" /></td>
    <td>Open flues</td><td><input class="ventilation" name="number_of_openflues" type="text" style="width:50px" /></td>
    <td>Intermittent fans</td><td><input class="ventilation" name="number_of_intermittentfans" type="text" style="width:50px" /></td>
  </tr>
  <tr>
    <td>Passive vents</td><td><input class="ventilation" name="number_of_passivevents" type="text" style="width:50px" /></td>
    <td>Flueless gas fires</td><td><input class="ventilation" name="number_of_fluelessgasfires" type="text" style="width:50px" /></td>    
    <td></td><td></td>
  </tr>
  
  <tbody id="structural">
  <tr><th>Structural infiltration</th><th></th><th></th><th></th><th></th><th></th></tr>
  <tr>
    <td>Walls</td>
    <td>
      <select class="ventilation" name="dwelling_construction">
        <option value='timberframe'>Timber Frame (+0.2)</option>
        <option value='masonry'>Masonry (+0.35)</option>
      </select>
    </td>
    <td>Percentage of windows and<br>doors draught proofed</td>
    <td>
    <div class="input-append">
      <input class="ventilation" name="percentage_draught_proofed" type="text" style="width:50px">
      <span class="add-on">%</span>
    </div>
    </td>  
    <td>Number of storeys</td><td><input class="ventilation" name="dwelling_storeys" type="text" style="width:50px" /></td>
  </tr>
  <tr>
    <td>Floor</td>
    <td>
      <select class="ventilation" name="suspended_wooden_floor">
        <option value="unsealed" >Suspended unsealed wooden floor (+0.2)</option>
        <option value="sealed">Suspended sealed wooden floor (+0.1)</option>
        <option value="0">Solid floor (+0)</option>
      </select>
    </td>
    <td>Draught Lobby</td>
    <td><input class="ventilation" name="draught_lobby" type="checkbox" /></td>
    <td>Number of sides sheltered</td><td><input class="ventilation" name="number_of_sides_sheltered" type="text" style="width:50px" /></td>
  </tr>
  </tbody>
</table>

<hr>

<div class="input-prepend">
<span class="add-on">Select ventilation type:</span>

<select class="ventilation" name="ventilation_type" style="width:500px">
  <option value='a'>Balanced mechanical ventilation with heat recovery (MVHR)</option>
  <option value='b'>Balanced mechanical ventilation without heat recovery (MV)</option>
  <option value='c'>Whole house extract ventilation or positive input ventilation from outside</option>
  <option value='d'>Natural ventilation or whole house positive input ventilation from loft</option>
</select>
</div><br>

<div id="system_air_change_rate">
  <span>Air change rate through the system (If exhaust air heat pump see Appendix N):</span>
  <input class="ventilation" name="system_air_change_rate" type="text" style="width:50px" /><br>
</div>

<div id="balanced_heat_recovery_efficiency">
  <span>Heat recovery efficiency allowing for in-use factor:</span>
  <div class="input-append"><input class="ventilation" name="balanced_heat_recovery_efficiency" type="text" style="width:50px" /><span class="add-on">%</span></div>
</div>

<table class="table">
<tr>
  <td></td>
  <td>Jan</td>
  <td>Feb</td>
  <td>Mar</td>
  <td>Apr</td>
  <td>May</td>
  <td>Jun</td>
  <td>Jul</td>
  <td>Aug</td>
  <td>Sep</td>
  <td>Oct</td>
  <td>Nov</td>
  <td>Dec</td>
  <td><b>Average</b></td>
</tr>
<tbody id="effective_air_change_rate"></tbody>
<tbody id="infiltration_WK"></tbody>
</table>
