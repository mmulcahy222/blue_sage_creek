from bs4 import BeautifulSoup
import pprint

def sanitize(word):
	return ''.join([x for x in str(word) if ord(x) < 128])

def file_get_contents(filename):
	f = open(filename, 'r', encoding="utf-8")
	r = f.read()
	r = sanitize(r)
	f.close()
	return r

def get_item(iterable, index, default=''):
	try:
		return operator.getitem(iterable, index)
	except:
		return default



#########################
#    Get data in KML
#########################
source_file = 'blue_sage_creek.kml'
destination_file = 'latlong_blue_sage_creek.php'
latlng_nodes = []
kml = file_get_contents(source_file)
kml = BeautifulSoup(kml,'lxml')
#Run through folders
for folder in kml.select('folder'):
	name = folder.find('name')
	#if blue_sage_creek folder
	if name is not None and name.text == 'blue_sage_creek':
		for i,placemark in enumerate(folder.select('placemark')):
			lot_no = placemark.find('name').text
			coordinates = placemark.find('coordinates').text.strip()
			#split coordinates & remove last element that is a repeat (it's now a list)
			coordinates = coordinates.split()[0:-1]
			latlng_nodes.append({'lot_no':lot_no,'coordinates':coordinates})


#WRITE TEXT FOR LATLNG_NODES (list of dicts)
#
# {'lot_no': '12', 'coordinates': ['-96.24623703243168,41.21986053964477,11', '-96.24623134987373,41.21949156102872,11', '-96.24598574315982,41.21950906450945,11', '-96.24598861523531,41.21984071368306,11']}
# {'lot_no': '13', 'coordinates': ['-96.24656577598323,41.21986626636284,4.999999999999999', '-96.2465646498837,41.21949903519391,4.999999999999999', '-96.24630109278755,41.21946713853502,4.999999999999999', '-96.24628483497666,41.21988307384939,4.999999999999999']}
#
#Beginning Text
latlng_coords_php = f'''
<?
global $coords;
$coords = [];
switch($lot_number){{
'''
for latlng_node in latlng_nodes:
	latlng_coords_php += f'''case {latlng_node['lot_no']}:\n'''
	latlng_coords_php += f'''$coords['center'] = "clat='41.22232029534923' clon='-96.24712618302574'";\n'''
	latlng_coords_php += f'''$coords['points'] = "'''
	coordinates_all_points = latlng_node['coordinates']
	for coordinates_comma in coordinates_all_points:
		coordinates = coordinates_comma.split(',')
		longitude = coordinates[0]
		latitude = coordinates[1]
		latlng_coords_php += f'''<point lat='{latitude}' lon='{longitude}' />\n'''
	latlng_coords_php += '''";\n'''	
	latlng_coords_php += 'break;\n'
latlng_coords_php += '}?>'

print(latlng_coords_php)