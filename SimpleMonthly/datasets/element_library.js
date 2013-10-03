// SAP 2012 and The Whole House Book by Pat Borer and Cindy Harris datasets

var element_library = {

  'roof0001': {type:'Roof', description:"Uninsulated loft", uvalue: 2.00, kvalue:9.0},
  'roof0002': {type:'Roof', description:"Loft with 100mm insulation", uvalue: 0.25, kvalue:9.0},
  'roof0003': {type:'Roof', description:"Room in the roof 200mm", uvalue: 0.2, kvalue:9.0},
  'roof0004': {type:'Roof', description:"Loft with 250mm insulation", uvalue: 0.16, kvalue:9.0},
  'roof0005': {type:'Roof', description:"Superinsulated 250mm insulation", uvalue: 0.14, kvalue:9.0},
  
  'wall0001': {type:'Wall', description:"Solid brick 225mm", uvalue: 2.20, kvalue:190.0},
  'wall0002': {type:'Wall', description:"Uninsulated cavity brick", uvalue: 1.30, kvalue:150.0},
  'wall0003': {type:'Wall', description:"Uninsulated cavity lightweight block", uvalue: 0.96, kvalue:130.0},
  'wall0004': {type:'Wall', description:"Cavity of timber frame wall with 50mm insulation", uvalue: 0.45, kvalue:10.0},
  'wall0005': {type:'Wall', description:"Cavity of timber frame wall with 100mm insulation", uvalue: 0.35, kvalue:10.0},
  'wall0006': {type:'Wall', description:"Superinsulated 250mm insulation", uvalue: 0.14, kvalue:10.0},

  // http://www.historic-scotland.gov.uk/hstp102011-u-values-and-traditional-buildings.pdf
  'wall0007': {type:'Wall', description:"Uninsulated 600mm stone wall finished with 'plaster on laths'", uvalue: 1.1, kvalue:190.0},
  'wall0008': {type:'Wall', description:"Uninsulated 600mm stone wall finished with 'plaster on the hard'", uvalue: 1.6, kvalue:190.0},
  'wall0009': {type:'Wall', description:"Uninsulated 600mm stone wall finished with plasterboard", uvalue: 0.9, kvalue:190.0},
  'wall0010': {type:'Wall', description:"Uninsulated 600mm stone wall finished with 'plaster on the hard' 1.3", uvalue: 1.1, kvalue:350.0},
  
  'floor0001': {type:'Floor', description:"Timber floor uninsulated", uvalue: 0.83, kvalue:20.0},
  'floor0002': {type:'Floor', description:"Timber floor with 150mm insulation", uvalue: 0.25, kvalue:20.0},
  'floor0003': {type:'Floor', description:"Timber floor, Superinsulated 250mm insulation", uvalue: 0.14, kvalue:20.0},
  'floor0004': {type:'Floor', description:"Solid floor uninsulated", uvalue: 0.7, kvalue:75.0},
  'floor0005': {type:'Floor', description:"Solid floor with 100mm insulation", uvalue: 0.25, kvalue:110.0},
  'floor0006': {type:'Floor', description:"Solid floor, Superinsulated 200mm insulation", uvalue: 0.15, kvalue:110.0},

  'floor0007': {type:'Floor', description:"Solid floor uninsulated with carpet", uvalue: 0.5, kvalue:110.0},


  //--------------------------------------------------------------------------------------------------------------------------
  
  // Double-glazed air filled
  
  'window0001': {type:'Window', description: "PVC or Wood frame, Double-glazed, air filled, 6mm gap", uvalue: 3.1, g: 0.76, gL: 0.8, ff:0.7},
  'window0002': {type:'Window', description: "PVC or Wood frame, Double-glazed, air filled, 12mm gap", uvalue: 2.8, g: 0.76, gL: 0.8, ff:0.7},
  'window0003': {type:'Window', description: "PVC or Wood frame, Double-glazed, air filled, 16 or more mm gap", uvalue: 2.7, g: 0.76, gL: 0.8, ff:0.7},

  'window0004': {type:'Window', description: "Metal frame, Double-glazed, air filled, 6mm gap", uvalue: 3.7, g: 0.76, gL: 0.8, ff:0.8},
  'window0005': {type:'Window', description: "Metal frame, Double-glazed, air filled, 12mm gap", uvalue: 3.4, g: 0.76, gL: 0.8, ff:0.8},
  'window0006': {type:'Window', description: "Metal frame, Double-glazed, air filled, 16 or more mm gap", uvalue: 3.3, g: 0.76, gL: 0.8, ff:0.8},

  // Double-glazed air filled (low-E, εn = 0.2, hard coat)
  
  'window0007': {type:'Window', description: "PVC or Wood frame, Double-glazed, air filled, (low-E, εn = 0.2, hard coat), 6mm gap ", uvalue: 2.7, g: 0.72, gL: 0.8, ff:0.7},
  'window0008': {type:'Window', description: "PVC or Wood frame, Double-glazed, air filled, (low-E, εn = 0.2, hard coat), 12mm gap", uvalue: 2.2, g: 0.72, gL: 0.8, ff:0.7},
  'window0009': {type:'Window', description: "PVC or Wood frame, Double-glazed, air filled (low-E, εn = 0.2, hard coat), 16 or more mm gap", uvalue: 2.1, g: 0.72, gL: 0.8, ff:0.7},

  'window0010': {type:'Window', description: "Metal frame, Double-glazed, air filled, (low-E, εn = 0.2, hard coat), 6mm gap", uvalue: 3.3, g: 0.72, gL: 0.8, ff:0.8},
  'window0011': {type:'Window', description: "Metal frame, Double-glazed, air filled, (low-E, εn = 0.2, hard coat), 12mm gap", uvalue: 2.8, g: 0.72, gL: 0.8, ff:0.8},
  'window0012': {type:'Window', description: "Metal frame, Double-glazed, air filled, (low-E, εn = 0.2, hard coat), 16 or more mm gap", uvalue: 2.6, g: 0.72, gL: 0.8, ff:0.8},

  // Double-glazed air filled (low-E, εn = 0.15, hard coat)

  'window0013': {type:'Window', description: "PVC or Wood frame, Double-glazed, air filled, (low-E, εn = 0.15, hard coat), 6mm gap ", uvalue: 2.7, g: 0.72, gL: 0.8, ff:0.7},
  'window0014': {type:'Window', description: "PVC or Wood frame, Double-glazed, air filled, (low-E, εn = 0.15, hard coat), 12mm gap", uvalue: 2.2, g: 0.72, gL: 0.8, ff:0.7},
  'window0015': {type:'Window', description: "PVC or Wood frame, Double-glazed, air filled (low-E, εn = 0.15, hard coat), 16 or more mm gap", uvalue: 2.0, g: 0.72, gL: 0.8, ff:0.7},

  'window0016': {type:'Window', description: "Metal frame, Double-glazed, air filled, (low-E, εn = 0.15, hard coat), 6mm gap", uvalue: 3.3, g: 0.72, gL: 0.8, ff:0.8},
  'window0017': {type:'Window', description: "Metal frame, Double-glazed, air filled, (low-E, εn = 0.15, hard coat), 12mm gap", uvalue: 2.7, g: 0.72, gL: 0.8, ff:0.8},
  'window0018': {type:'Window', description: "Metal frame, Double-glazed, air filled, (low-E, εn = 0.15, hard coat), 16 or more mm gap", uvalue: 2.5, g: 0.72, gL: 0.8, ff:0.8},
  
  // double-glazed, air filled (low-E, εn = 0.1, soft coat)

  'window0019': {type:'Window', description: "PVC or Wood frame, Double-glazed, air filled, (low-E, εn = 0.1, soft coat), 6mm gap ", uvalue: 2.6, g: 0.63, gL: 0.8, ff:0.7},
  'window0020': {type:'Window', description: "PVC or Wood frame, Double-glazed, air filled, (low-E, εn = 0.1, soft coat), 12mm gap", uvalue: 2.1, g: 0.63, gL: 0.8, ff:0.7},
  'window0021': {type:'Window', description: "PVC or Wood frame, Double-glazed, air filled (low-E, εn = 0.1, soft coat), 16 or more mm gap", uvalue: 1.9, g: 0.63, gL: 0.8, ff:0.7},

  'window0022': {type:'Window', description: "Metal frame, Double-glazed, air filled, (low-E, εn = 0.1, soft coat), 6mm gap", uvalue: 3.2, g: 0.63, gL: 0.8, ff:0.8},
  'window0023': {type:'Window', description: "Metal frame, Double-glazed, air filled, (low-E, εn = 0.1, soft coat), 12mm gap", uvalue: 2.6, g: 0.63, gL: 0.8, ff:0.8},
  'window0024': {type:'Window', description: "Metal frame, Double-glazed, air filled, (low-E, εn = 0.1, soft coat), 16 or more mm gap", uvalue: 2.4, g: 0.63, gL: 0.8, ff:0.8},

  // double-glazed, air filled (low-E, εn = 0.05, soft coat)

  'window0025': {type:'Window', description: "PVC or Wood frame, Double-glazed, air filled, (low-E, εn = 0.05, soft coat), 6mm gap ", uvalue: 2.6, g: 0.63, gL: 0.8, ff:0.7},
  'window0026': {type:'Window', description: "PVC or Wood frame, Double-glazed, air filled, (low-E, εn = 0.05, soft coat), 12mm gap", uvalue: 2.0, g: 0.63, gL: 0.8, ff:0.7},
  'window0027': {type:'Window', description: "PVC or Wood frame, Double-glazed, air filled (low-E, εn = 0.05, soft coat), 16 or more mm gap", uvalue: 1.8, g: 0.63, gL: 0.8, ff:0.7},

  'window0028': {type:'Window', description: "Metal frame, Double-glazed, air filled, (low-E, εn = 0.05, soft coat), 6mm gap", uvalue: 3.2, g: 0.63, gL: 0.8, ff:0.8},
  'window0029': {type:'Window', description: "Metal frame, Double-glazed, air filled, (low-E, εn = 0.05, soft coat), 12mm gap", uvalue: 2.5, g: 0.63, gL: 0.8, ff:0.8},
  'window0030': {type:'Window', description: "Metal frame, Double-glazed, air filled, (low-E, εn = 0.05, soft coat), 16 or more mm gap", uvalue: 2.3, g: 0.63, gL: 0.8, ff:0.8},
  
  //--------------------------------------------------------------------------------------------------------------------------
  
  // double-glazed, argon filled 
  
  'window0031': {type:'Window', description: "PVC or Wood frame, Double-glazed, argon filled, 6mm gap ", uvalue: 2.9, g: 0.76, gL: 0.8, ff:0.7},
  'window0032': {type:'Window', description: "PVC or Wood frame, Double-glazed, argon filled, 12mm gap", uvalue: 2.7, g: 0.76, gL: 0.8, ff:0.7},
  'window0033': {type:'Window', description: "PVC or Wood frame, Double-glazed, argon filled, 16 or more mm gap", uvalue: 2.6, g: 0.76, gL: 0.8, ff:0.7},

  'window0034': {type:'Window', description: "Metal frame, Double-glazed, argon filled, 6mm gap", uvalue: 3.5, g: 0.76, gL: 0.8, ff:0.8},
  'window0035': {type:'Window', description: "Metal frame, Double-glazed, argon filled, 12mm gap", uvalue: 3.3, g: 0.76, gL: 0.8, ff:0.8},
  'window0036': {type:'Window', description: "Metal frame, Double-glazed, argon filled, 16 or more mm gap", uvalue: 3.2, g: 0.76, gL: 0.8, ff:0.8},

  // double-glazed, argon filled (low-E, εn = 0.2, hard coat)

  'window0037': {type:'Window', description: "PVC or Wood frame, Double-glazed, argon filled (low-E, εn = 0.2, hard coat), 6mm gap ", uvalue: 2.5, g: 0.72, gL: 0.8, ff:0.7},
  'window0038': {type:'Window', description: "PVC or Wood frame, Double-glazed, argon filled (low-E, εn = 0.2, hard coat), 12mm gap", uvalue: 2.1, g: 0.72, gL: 0.8, ff:0.7},
  'window0039': {type:'Window', description: "PVC or Wood frame, Double-glazed, argon filled (low-E, εn = 0.2, hard coat), 16 or more mm gap", uvalue: 2.0, g: 0.72, gL: 0.8, ff:0.7},

  'window0040': {type:'Window', description: "Metal frame, Double-glazed, argon filled (low-E, εn = 0.2, hard coat), 6mm gap", uvalue: 3.0, g: 0.72, gL: 0.8, ff:0.8},
  'window0041': {type:'Window', description: "Metal frame, Double-glazed, argon filled (low-E, εn = 0.2, hard coat), 12mm gap", uvalue: 2.6, g: 0.72, gL: 0.8, ff:0.8},
  'window0042': {type:'Window', description: "Metal frame, Double-glazed, argon filled (low-E, εn = 0.2, hard coat), 16 or more mm gap", uvalue: 2.5, g: 0.72, gL: 0.8, ff:0.8},

  // double-glazed, argon filled (low-E, εn = 0.15, hard coat)

  'window0043': {type:'Window', description: "PVC or Wood frame, Double-glazed, argon filled (low-E, εn = 0.15, hard coat), 6mm gap ", uvalue: 2.4, g: 0.72, gL: 0.8, ff:0.7},
  'window0044': {type:'Window', description: "PVC or Wood frame, Double-glazed, argon filled (low-E, εn = 0.15, hard coat), 12mm gap", uvalue: 2.0, g: 0.72, gL: 0.8, ff:0.7},
  'window0045': {type:'Window', description: "PVC or Wood frame, Double-glazed, argon filled (low-E, εn = 0.15, hard coat), 16 or more mm gap", uvalue: 1.9, g: 0.72, gL: 0.8, ff:0.7},

  'window0046': {type:'Window', description: "Metal frame, Double-glazed, argon filled (low-E, εn = 0.15, hard coat), 6mm gap", uvalue: 3.0, g: 0.72, gL: 0.8, ff:0.8},
  'window0047': {type:'Window', description: "Metal frame, Double-glazed, argon filled (low-E, εn = 0.15, hard coat), 12mm gap", uvalue: 2.5, g: 0.72, gL: 0.8, ff:0.8},
  'window0048': {type:'Window', description: "Metal frame, Double-glazed, argon filled (low-E, εn = 0.15, hard coat), 16 or more mm gap", uvalue: 2.4, g: 0.72, gL: 0.8, ff:0.8},

  // double-glazed, argon filled (low-E, εn = 0.1, soft coat)

  'window0049': {type:'Window', description: "PVC or Wood frame, Double-glazed, argon filled (low-E, εn = 0.1, soft coat), 6mm gap ", uvalue: 2.3, g: 0.63, gL: 0.8, ff:0.7},
  'window0050': {type:'Window', description: "PVC or Wood frame, Double-glazed, argon filled (low-E, εn = 0.1, soft coat), 12mm gap", uvalue: 1.9, g: 0.63, gL: 0.8, ff:0.7},
  'window0051': {type:'Window', description: "PVC or Wood frame, Double-glazed, argon filled (low-E, εn = 0.1, soft coat), 16 or more mm gap", uvalue: 1.8, g: 0.63, gL: 0.8, ff:0.7},

  'window0052': {type:'Window', description: "Metal frame, Double-glazed, argon filled (low-E, εn = 0.1, soft coat), 6mm gap", uvalue: 2.9, g: 0.63, gL: 0.8, ff:0.8},
  'window0053': {type:'Window', description: "Metal frame, Double-glazed, argon filled (low-E, εn = 0.1, soft coat), 12mm gap", uvalue: 2.4, g: 0.63, gL: 0.8, ff:0.8},
  'window0054': {type:'Window', description: "Metal frame, Double-glazed, argon filled (low-E, εn = 0.1, soft coat), 16 or more mm gap", uvalue: 2.3, g: 0.63, gL: 0.8, ff:0.8},

  // double-glazed, argon filled (low-E, εn = 0.05, soft coat)

  'window0055': {type:'Window', description: "PVC or Wood frame, Double-glazed, argon filled (low-E, εn = 0.05, soft coat), 6mm gap ", uvalue: 2.3, g: 0.63, gL: 0.8, ff:0.7},
  'window0056': {type:'Window', description: "PVC or Wood frame, Double-glazed, argon filled (low-E, εn = 0.05, soft coat), 12mm gap", uvalue: 1.8, g: 0.63, gL: 0.8, ff:0.7},
  'window0057': {type:'Window', description: "PVC or Wood frame, Double-glazed, argon filled (low-E, εn = 0.05, soft coat), 16 or more mm gap", uvalue: 1.7, g: 0.63, gL: 0.8, ff:0.7},

  'window0058': {type:'Window', description: "Metal frame, Double-glazed, argon filled (low-E, εn = 0.05, soft coat), 6mm gap", uvalue: 2.8, g: 0.63, gL: 0.8, ff:0.8},
  'window0059': {type:'Window', description: "Metal frame, Double-glazed, argon filled (low-E, εn = 0.05, soft coat), 12mm gap", uvalue: 2.2, g: 0.63, gL: 0.8, ff:0.8},
  'window0060': {type:'Window', description: "Metal frame, Double-glazed, argon filled (low-E, εn = 0.05, soft coat), 16 or more mm gap", uvalue: 2.1, g: 0.63, gL: 0.8, ff:0.8},

  //--------------------------------------------------------------------------------------------------------------------------

  'window0061': {type:'Window', description: "PVC or Wood frame, triple glazed, air filled, 6mm gap ", uvalue: 2.4, g: 0.68, gL: 0.7, ff:0.7},
  'window0062': {type:'Window', description: "PVC or Wood frame, triple glazed, air filled, 12mm gap", uvalue: 2.1, g: 0.68, gL: 0.7, ff:0.7},
  'window0063': {type:'Window', description: "PVC or Wood frame, triple glazed, air filled, 16 or more mm gap", uvalue: 2.0, g: 0.68, gL: 0.7, ff:0.7},
  
  'window0064': {type:'Window', description: "Metal frame, triple glazed, air filled, 6mm gap ", uvalue: 2.9, g: 0.68, ff:0.8},
  'window0065': {type:'Window', description: "Metal frame, triple glazed, air filled, 12mm gap", uvalue: 2.6, g: 0.68, ff:0.8},
  'window0066': {type:'Window', description: "Metal frame, triple glazed, air filled, 16 or more mm gap", uvalue: 2.5, g: 0.68, ff:0.8},
  
  

  'window0067': {type:'Window', description: "PVC or Wood frame, triple-glazed, air filled (low-E, εn = 0.2, hard coat), 6mm gap ", uvalue: 2.1, g: 0.64, gL: 0.7, ff:0.7},
  'window0068': {type:'Window', description: "PVC or Wood frame, triple-glazed, air filled (low-E, εn = 0.2, hard coat), 12mm gap", uvalue: 1.7, g: 0.64, gL: 0.7, ff:0.7},
  'window0069': {type:'Window', description: "PVC or Wood frame, triple-glazed, air filled (low-E, εn = 0.2, hard coat), 16 or more mm gap", uvalue: 1.6, g: 0.64, gL: 0.7, ff:0.7},
  
  'window0070': {type:'Window', description: "Metal frame, triple-glazed, air filled (low-E, εn = 0.2, hard coat), 6mm gap ", uvalue: 2.6, g: 0.64, gL: 0.7, ff:0.8},
  'window0071': {type:'Window', description: "Metal frame, triple-glazed, air filled (low-E, εn = 0.2, hard coat), 12mm gap", uvalue: 2.1, g: 0.64, gL: 0.7, ff:0.8},
  'window0072': {type:'Window', description: "Metal frame, triple-glazed, air filled (low-E, εn = 0.2, hard coat), 16 or more mm gap", uvalue: 2.0, g: 0.64, gL: 0.7, ff:0.8},
  
  

  'window0073': {type:'Window', description: "PVC or Wood frame, triple-glazed, air filled (low-E, εn = 0.15, hard coat), 6mm gap ", uvalue: 2.1, g: 0.64, gL: 0.7, ff:0.7},
  'window0074': {type:'Window', description: "PVC or Wood frame, triple-glazed, air filled (low-E, εn = 0.15, hard coat), 12mm gap", uvalue: 1.7, g: 0.64, gL: 0.7, ff:0.7},
  'window0075': {type:'Window', description: "PVC or Wood frame, triple-glazed, air filled (low-E, εn = 0.15, hard coat), 16 or more mm gap", uvalue: 1.6, g: 0.64, gL: 0.7, ff:0.7},
  
  'window0076': {type:'Window', description: "Metal frame, triple-glazed, air filled (low-E, εn = 0.15, hard coat), 6mm gap ", uvalue: 2.5, g: 0.64, gL: 0.7, ff:0.8},
  'window0077': {type:'Window', description: "Metal frame, triple-glazed, air filled (low-E, εn = 0.15, hard coat), 12mm gap", uvalue: 2.1, g: 0.64, gL: 0.7, ff:0.8},
  'window0078': {type:'Window', description: "Metal frame, triple-glazed, air filled (low-E, εn = 0.15, hard coat), 16 or more mm gap", uvalue: 2.0, g: 0.64, gL: 0.7, ff:0.8},
  
  

  'window0079': {type:'Window', description: "PVC or Wood frame, triple-glazed, air filled (low-E, εn = 0.1, soft coat), 6mm gap ", uvalue: 2.0, g: 0.57, gL: 0.7, ff:0.7},
  'window0080': {type:'Window', description: "PVC or Wood frame, triple-glazed, air filled (low-E, εn = 0.1, soft coat), 12mm gap", uvalue: 1.6, g: 0.57, gL: 0.7, ff:0.7},
  'window0081': {type:'Window', description: "PVC or Wood frame, triple-glazed, air filled (low-E, εn = 0.1, soft coat), 16 or more mm gap", uvalue: 1.5, g: 0.57, gL: 0.7, ff:0.7},
  
  'window0082': {type:'Window', description: "Metal frame, triple-glazed, air filled (low-E, εn = 0.1, soft coat), 6mm gap ", uvalue: 2.5, g: 0.57, gL: 0.7, ff:0.8},
  'window0083': {type:'Window', description: "Metal frame, triple-glazed, air filled (low-E, εn = 0.1, soft coat), 12mm gap", uvalue: 2.0, g: 0.57, gL: 0.7, ff:0.8},
  'window0084': {type:'Window', description: "Metal frame, triple-glazed, air filled (low-E, εn = 0.1, soft coat), 16 or more mm gap", uvalue: 1.9, g: 0.57, gL: 0.7, ff:0.8},
  
  

  'window0085': {type:'Window', description: "PVC or Wood frame, triple-glazed, air filled (low-E, εn = 0.05, soft coat), 6mm gap ", uvalue: 1.9, g: 0.57, gL: 0.7, ff:0.7},
  'window0086': {type:'Window', description: "PVC or Wood frame, triple-glazed, air filled (low-E, εn = 0.05, soft coat), 12mm gap", uvalue: 1.5, g: 0.57, gL: 0.7, ff:0.7},
  'window0087': {type:'Window', description: "PVC or Wood frame, triple-glazed, air filled (low-E, εn = 0.05, soft coat), 16 or more mm gap", uvalue: 1.4, g: 0.57, gL: 0.7, ff:0.7},
  
  'window0088': {type:'Window', description: "Metal frame, triple-glazed, air filled (low-E, εn = 0.05, soft coat), 6mm gap ", uvalue: 2.4, g: 0.57, gL: 0.7, ff:0.8},
  'window0089': {type:'Window', description: "Metal frame, triple-glazed, air filled (low-E, εn = 0.05, soft coat), 12mm gap", uvalue: 1.9, g: 0.57, gL: 0.7, ff:0.8},
  'window0090': {type:'Window', description: "Metal frame, triple-glazed, air filled (low-E, εn = 0.05, soft coat), 16 or more mm gap", uvalue: 1.8, g: 0.57, gL: 0.7, ff:0.8},
  
  

  'window0091': {type:'Window', description: "PVC or Wood frame, triple-glazed, argon filled, 6mm gap ", uvalue: 2.2, g: 0.68, gL: 0.7, ff:0.7},
  'window0092': {type:'Window', description: "PVC or Wood frame, triple-glazed, argon filled, 12mm gap", uvalue: 2.0, g: 0.68, gL: 0.7, ff:0.7},
  'window0093': {type:'Window', description: "PVC or Wood frame, triple-glazed, argon filled, 16 or more mm gap", uvalue: 1.9, g: 0.68, gL: 0.7, ff:0.7},
  
  'window0094': {type:'Window', description: "Metal frame, triple-glazed, argon filled, 6mm gap ", uvalue: 2.8, g: 0.68, gL: 0.7, ff:0.8},
  'window0095': {type:'Window', description: "Metal frame, triple-glazed, argon filled, 12mm gap", uvalue: 2.5, g: 0.68, gL: 0.7, ff:0.8},
  'window0096': {type:'Window', description: "Metal frame, triple-glazed, argon filled, 16 or more mm gap", uvalue: 2.4, g: 0.68, gL: 0.7, ff:0.8},
  
  

  'window0097': {type:'Window', description: "PVC or Wood frame, triple-glazed, argon filled (low-E, εn = 0.2, hard coat), 6mm gap ", uvalue: 1.9, g: 0.64, gL: 0.7, ff:0.7},
  'window0098': {type:'Window', description: "PVC or Wood frame, triple-glazed, argon filled (low-E, εn = 0.2, hard coat), 12mm gap", uvalue: 1.6, g: 0.64, gL: 0.7, ff:0.7},
  'window0099': {type:'Window', description: "PVC or Wood frame, triple-glazed, argon filled (low-E, εn = 0.2, hard coat), 16 or more mm gap", uvalue: 1.5, g: 0.64, gL: 0.7, ff:0.7},
  
  'window0100': {type:'Window', description: "Metal frame, triple-glazed, argon filled (low-E, εn = 0.2, hard coat), 6mm gap ", uvalue: 2.3, g: 0.64, gL: 0.7, ff:0.8},
  'window0101': {type:'Window', description: "Metal frame, triple-glazed, argon filled (low-E, εn = 0.2, hard coat), 12mm gap", uvalue: 2.0, g: 0.64, gL: 0.7, ff:0.8},
  'window0102': {type:'Window', description: "Metal frame, triple-glazed, argon filled (low-E, εn = 0.2, hard coat), 16 or more mm gap", uvalue: 1.9, g: 0.64, gL: 0.7, ff:0.8},
  
  

  'window0103': {type:'Window', description: "PVC or Wood frame, triple-glazed, argon filled (low-E, εn = 0.15, hard coat), 6mm gap ", uvalue: 1.8, g: 0.64, gL: 0.7, ff:0.7},
  'window0104': {type:'Window', description: "PVC or Wood frame, triple-glazed, argon filled (low-E, εn = 0.15, hard coat), 12mm gap", uvalue: 1.5, g: 0.64, gL: 0.7, ff:0.7},
  'window0105': {type:'Window', description: "PVC or Wood frame, triple-glazed, argon filled (low-E, εn = 0.15, hard coat), 16 or more mm gap", uvalue: 1.4, g: 0.64, gL: 0.7, ff:0.7},
  
  'window0106': {type:'Window', description: "Metal frame, triple-glazed, argon filled (low-E, εn = 0.15, hard coat), 6mm gap ", uvalue: 2.3, g: 0.64, gL: 0.7, ff:0.8},
  'window0107': {type:'Window', description: "Metal frame, triple-glazed, argon filled (low-E, εn = 0.15, hard coat), 12mm gap", uvalue: 1.9, g: 0.64, gL: 0.7, ff:0.8},
  'window0108': {type:'Window', description: "Metal frame, triple-glazed, argon filled (low-E, εn = 0.15, hard coat), 16 or more mm gap", uvalue: 1.8, g: 0.64, gL: 0.7, ff:0.8},
  
  

  'window0109': {type:'Window', description: "PVC or Wood frame, triple-glazed, argon filled (low-E, εn = 0.1, soft coat), 6mm gap ", uvalue: 1.8, g: 0.57, gL: 0.7, ff:0.7},
  'window0110': {type:'Window', description: "PVC or Wood frame, triple-glazed, argon filled (low-E, εn = 0.1, soft coat), 12mm gap", uvalue: 1.5, g: 0.57, gL: 0.7, ff:0.7},
  'window0111': {type:'Window', description: "PVC or Wood frame, triple-glazed, argon filled (low-E, εn = 0.1, soft coat), 16 or more mm gap", uvalue: 1.4, g: 0.57, gL: 0.7, ff:0.7},
  
  'window0112': {type:'Window', description: "Metal frame, triple-glazed, argon filled (low-E, εn = 0.1, soft coat), 6mm gap ", uvalue: 2.2, g: 0.57, gL: 0.7, ff:0.8},
  'window0113': {type:'Window', description: "Metal frame, triple-glazed, argon filled (low-E, εn = 0.1, soft coat), 12mm gap", uvalue: 1.9, g: 0.57, gL: 0.7, ff:0.8},
  'window0114': {type:'Window', description: "Metal frame, triple-glazed, argon filled (low-E, εn = 0.1, soft coat), 16 or more mm gap", uvalue: 1.8, g: 0.57, gL: 0.7, ff:0.8},
  
  

  'window0115': {type:'Window', description: "PVC or Wood frame, triple-glazed, argon filled (low-E, εn = 0.05, soft coat), 6mm gap ", uvalue: 1.7, g: 0.57, gL: 0.7, ff:0.7},
  'window0116': {type:'Window', description: "PVC or Wood frame, triple-glazed, argon filled (low-E, εn = 0.05, soft coat), 12mm gap", uvalue: 1.4, g: 0.57, gL: 0.7, ff:0.7},
  'window0117': {type:'Window', description: "PVC or Wood frame, triple-glazed, argon filled (low-E, εn = 0.05, soft coat), 16 or more mm gap", uvalue: 1.3, g: 0.57, gL: 0.7, ff:0.7},
  
  'window0118': {type:'Window', description: "Metal frame, triple-glazed, argon filled (low-E, εn = 0.05, soft coat), 6mm gap ", uvalue: 2.2, g: 0.57, gL: 0.7, ff:0.8},
  'window0119': {type:'Window', description: "Metal frame, triple-glazed, argon filled (low-E, εn = 0.05, soft coat), 12mm gap", uvalue: 1.8, g: 0.57, gL: 0.7, ff:0.8},
  'window0120': {type:'Window', description: "Metal frame, triple-glazed, argon filled (low-E, εn = 0.05, soft coat), 16 or more mm gap", uvalue: 1.7, g: 0.57, gL: 0.7, ff:0.8},


  'window0121': {type:'Window', description: "PVC or Wood frame Windows and doors, single-glazed", uvalue: 4.8, g: 0.85, gL: 0.9, ff:0.7},
  'window0122': {type:'Window', description: "Metal frame Windows and doors, single-glazed", uvalue: 5.7, g: 0.85, gL: 0.9, ff:0.8},

  'window0123': {type:'Window', description: "PVC or Wood frame, Window with secondary glazing", uvalue: 2.4, g: 0.76, gL: 0.8, ff:0.7}

};

  // 'window0001': {type:'Window', description: "Solid wooden door to outside", uvalue: 3.0},
  // 'window0001': {type:'Window', description: "Solid wooden door to unheated corridor", uvalue: 1.4],









