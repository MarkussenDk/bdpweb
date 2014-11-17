dependencies = {
            layers: [
                   /* {
                            // This layer will be discarded, it is just used
                            // to specify some modules that should not be included
                            // in a later layer, but something that should not be
                            // saved as an actual layer output. The important property
                            // is the "discard" property. If set to true, then the layer
                            // will not be a saved layer in the release directory.
                            //name: "acme.discard",
                            //resourceName: "acme.discard",
                            //discard: true,
                            // Path to the copyright file must be relative to
                            // the util/buildscripts directory, or an absolute path.
                            //copyrightFile: "myCopyright.txt",
                            dependencies: [
                                    "dojo.string"
                            ]
                    }, */
                    {
                        name: "dojo.js",
                        customBase: true,
                        dependencies: [
                                //"dojo._base.Deferred",
                                //"dojo._base.array",
                                "dojox.grid.DataGrid",
                                "dojox.data.QueryReadStore"
                        ]
                    }
                    
                    
                    ],

                    prefixes: [
                        // the system knows where to find the "dojo/" directory, but we
                        // need to tell it about everything else. Directories listed here
                        // are, at a minimum, copied to the build directory.
                        [ "dijit", "../dijit" ],
                        [ "dojox", "../dojox" ],
                        [ "css", "../css" ],
                        [ "bdp",  "../bdp" ]
                    ]
                }