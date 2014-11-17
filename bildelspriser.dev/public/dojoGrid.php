<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" 
	"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title>Test dojox.grid.Grid Basic</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"></meta>
	<style type="text/css">
		@import "/js/dojo/resources/Grid.css";
		@import "/js/dojo/resources/tundraGrid.css";
		@import "/js/dojo/resources/dojo.css";
		@import "/js/dijit/themes/tundra/tundra.css";
		body {
			font-size: 0.9em;
			font-family: Geneva, Arial, Helvetica, sans-serif;
		}
		.heading {
			font-weight: bold;
			padding-bottom: 0.25em;
		}
				
		#grid {
			border: 1px solid #333;
			width: 35em;
			height: 30em;
		}
	</style>
	<script type="text/javascript" src="/js/dojo/dojo.js" 
		djConfig="isDebug:false, parseOnLoad: true"></script>
	<script type="text/javascript">
		dojo.require("dijit.dijit");
		dojo.require("dojox.grid.DataGrid");
		dojo.require("dojo.data.ItemFileWriteStore");
		dojo.require("dojo.parser");
	</script>
	<script type="text/javascript" src="http://archive.dojotoolkit.org/nightly/dojotoolkit/dojox/grid/tests/support/test_data.js"></script>
	<script2 type="text/javascript">
		var layout = [[
			new dojox.grid.cells.RowIndex({ width: 5 }),
			{name: 'Column 1', field: 'col1'},
			{name: 'Column 2', field: 'col2'},
			{name: 'Column 3', field: 'col3'},
			{name: 'Column 4', field: 'col4', width: "150px"},
			{name: 'Column 5', field: 'col5'}
		],[
			{name: 'Column 6', field: 'col6', colSpan: 2},
			{name: 'Column 7', field: 'col7'},
			{name: 'Column 8'},
			{name: 'Column 9', field: 'col3', colSpan: 2}
		]];
	</script2>
</head>
<body class="tundra">
	<div class="heading">dojox.grid.Grid Basic Test</div>
	<div jsId="grid" id="grid" dojoType="dojox.grid.DataGrid" 
		store="test_store" query="{ id: '*' }" 
		structure="layout" rowSelector="20px"></div>
</body>
</html>