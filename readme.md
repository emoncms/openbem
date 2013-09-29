## OpenBEM - Open Source Building Energy Model

This work is all open source - under GPL.
If youd like to help with development or have any questions feel free to email me trystan.lea@gmail.com 

OpenBEM is a whole house energy toolkit, currently in an early stage of development.

### Simple monthly model

The simple monthly model was originally developed as the SAP 2012 module. The aim of OpenBEM version of it is to focus more on creating a tool thats useful for design rather than compliance which is SAP's main focus, although it is intended that OpenBEM will have a SAP 2012 compliant mode.

The aim of the OpenBEM interface is to make it possible to get results as fast as possible, the core part of the building energy model is contained on the one page. For the more extensive SAP calculation sheets such as internal gains, ventilation and water heating there will be the option to go off the main interface page. 

There will also be the option to compare using SAP's temperature reduction equation with entering in the mean internal temperature manually, allowing for better appreciation of the differences.

The SAP temperature reduction equation is an important part of the model, in certain buildings (low thermal mass, high heat loss) the energy savings from dynamic heating vs constant heating can be very significant.

The dynamic modelling approach explored on the simulation page and in the dynamic_model spreadsheet explores this aspect further. Further investigation is needed to see if the SAP temperature reduction equation can be derived from these dynamic models.

### Dynamic model based coheating test 

A multi-stage dynamic RC type model of a building appears to give a good fit to monitored internal temperature in a simple building.

### Spreadsheet version of dynamic model

See: dynamic_model.ods

### Daily average based coheating test

The aim here is to replicate the current approach of coheating data analysis. Using daily averages and linear regression.



