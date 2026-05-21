<?php

function getDisplayMap($designId)
{
    $maps = [
        
        85 => [            
            "Sleeve Number Left" => [
                "items" => [
                    "b_rightnumber" => ["x" => 5.95, "y" => 1.69, "rotate" => 90,"size" => 25],
                    "f_leftnumber" => ["x" => 2.65, "y" => 9.5, "rotate" => 90, "size" => 25]
                ]
            ],            
            "Sleeve Number Right" => [
                "items" => [
                    "b_leftnumber" => ["x" => 2, "y" => 1.69, "rotate" =>  270,"size" => 25],
                    "f_rightnumber" => ["x" => 1.4, "y" => 9.5, "rotate" => 270,"size" =>25] // 👈 front sleeve
                ]
            ],
            "Front Image" => [
                "items" => [
                    "front_logo" => ["x" => 2.03, "y" => 4.6, "rotate" => 0]
                ]
            ],
            "Front" => [
                "items" => [
                    "front_logo" => ["x" => 1.75, "y" => 3.4, "rotate" => 0, "height" => 80, "width" => 80]
                ]
            ],    
            "Front Logo" => [
                "items" => [
                    "front_logo" => ["x" => 1.75, "y" => 3.4, "rotate" => 0, "height" => 80, "width" => 80]
                ]
            ],    
            "Back Name" => [
                "items" => [
                    "player_name" => ["x" => 3, "y" => 1.48, "rotate" => 0, "size" => 25]
                ]
            ],            
            "Number Back" => [
                "items" => [
                    "player_number" => ["x" => 3.0, "y" => 1.29, "rotate" => 0, "size" => 60]
                ]
            ],
            "Right Shoulder" => [
                "items" => [
                    "lslf" => ["x" => 3.68, "y" => 3.3, "rotate" =>  270],
                    "lslf-2" => ["x" => 1.7, "y" => 56, "rotate" => 270] // 👈 front sleeve
                ]
            ],
            "Shoulder  Right" => [
                "items" => [
                    "lslf" => ["x" => 3.68, "y" => 3.3, "rotate" =>  270, "height" => 30, "width" => 30],
                    "lslf-4" => ["x" => 1.51, "y" => 400, "rotate" => 270, "height" => 30, "width" => 30] // 👈 front sleeve
                ]
            ],    
            "Left Shoulder" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslb" => ["x" => 5.35, "y" => 4.7, "rotate" => 90]
                ]
            ],
            "Shoulder  Left" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslf-3" => ["x" => 2.45, "y" => 5.4, "rotate" => 90 , "height" => 30, "width" => 30]
                ]
            ],    
            "Neck Patch" => [
                "items" => [
                    "l_shoulsadasder_logo_b" => ["x" => 2.2, "y" => 7.1, "rotate" => 90]
                ]
            ],
            "Neck" => [
                "items" => [
                    "l_shoulsadasder_logo_b" => ["x" => 2.2, "y" => 7.1, "rotate" => 90]
                ]
            ],
            "Logo" => [
                "items" => [
                    "teamname" => ["x" => 1.7, "y" => 3.0, "rotate" => 0, "height" => 50, "width" => 50]
                ]
            ],
            "Text Style 1" => [
                "items" => [
                    "teamname" => ["x" => 1.85, "y" => 3.9, "rotate" => 0, "size" => 20]
                ]
            ],
            "Front Number" => [
                "items" => [
                    "front_number" => ["x" => 1.85, "y" => 3.0, "rotate" => 0, "size" => 20]
                ]
            ],
            "Upper Chest" => [
                "items" => [
                    "Upper_chest_flag" => ["x" => 1.85, "y" => 3.4, "rotate" => 0, "height" => 30, "width" => 30]
                ]
            ],
            "Upper Chest Logo Left" => [
                "items" => [
                    "Upper_chest_flag" => ["x" => 1.85, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
            "Upper Chest Logo Right" => [
                "items" => [
                    "front_crest_logo" => ["x" => 1.45, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
            "Shoulder Logo Left" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslf-3" => ["x" => 2.55, "y" => 5.6, "rotate" => 90 , "height" => 30, "width" => 30],
                    "lslf-2" => ["x" => 3.2, "y" => 2.1, "rotate" => 270, "height" => 30, "width" => 30] 
                ]
            ],
            "Shoulder Logo Right" => [
                "items" => [                    
                    "lslf-4" => ["x" => 1.43, "y" => 400, "rotate" => 270, "height" => 30, "width" => 30], // 👈 front sleeve
                    "lslf" => ["x" => 2.8, "y" => 1.52, "rotate" =>  90, "height" => 30, "width" => 30],
                ]
            ],
        ],
        106 => [            
            "Sleeve Number Left" => [
                "items" => [
                    "b_rightnumber" => ["x" => 5.95, "y" => 1.69, "rotate" => 90,"size" => 25],
                    "f_leftnumber" => ["x" => 2.65, "y" => 9.5, "rotate" => 90, "size" => 25]
                ]
            ],            
            "Sleeve Number Right" => [
                "items" => [
                    "b_leftnumber" => ["x" => 2, "y" => 1.69, "rotate" =>  270,"size" => 25],
                    "f_rightnumber" => ["x" => 1.4, "y" => 9.5, "rotate" => 270,"size" =>25] // 👈 front sleeve
                ]
            ],
            "Front Image" => [
                "items" => [
                    "front_logo" => ["x" => 2.03, "y" => 4.6, "rotate" => 0]
                ]
            ],
            "Front" => [
                "items" => [
                    "front_logo" => ["x" => 1.75, "y" => 3.4, "rotate" => 0, "height" => 80, "width" => 80]
                ]
            ],    
            "Front Logo" => [
                "items" => [
                    "front_logo" => ["x" => 1.75, "y" => 3.4, "rotate" => 0, "height" => 80, "width" => 80]
                ]
            ],    
            "Back Name" => [
                "items" => [
                    "player_name" => ["x" => 3, "y" => 1.48, "rotate" => 0, "size" => 25]
                ]
            ],            
            "Number Back" => [
                "items" => [
                    "player_number" => ["x" => 3.0, "y" => 1.29, "rotate" => 0, "size" => 60]
                ]
            ],
            "Right Shoulder" => [
                "items" => [
                    "lslf" => ["x" => 3.68, "y" => 3.3, "rotate" =>  270],
                    "lslf-2" => ["x" => 1.7, "y" => 56, "rotate" => 270] // 👈 front sleeve
                ]
            ],
            "Shoulder  Right" => [
                "items" => [
                    "lslf" => ["x" => 3.68, "y" => 3.3, "rotate" =>  270, "height" => 30, "width" => 30],
                    "lslf-4" => ["x" => 1.51, "y" => 400, "rotate" => 270, "height" => 30, "width" => 30] // 👈 front sleeve
                ]
            ],    
            "Left Shoulder" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslb" => ["x" => 5.35, "y" => 4.7, "rotate" => 90]
                ]
            ],
            "Shoulder  Left" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslf-3" => ["x" => 2.45, "y" => 5.4, "rotate" => 90 , "height" => 30, "width" => 30]
                ]
            ],    
            "Neck Patch" => [
                "items" => [
                    "l_shoulsadasder_logo_b" => ["x" => 2.2, "y" => 7.1, "rotate" => 90]
                ]
            ],
            "Logo" => [
                "items" => [
                    "teamname" => ["x" => 1.7, "y" => 3.0, "rotate" => 0, "height" => 50, "width" => 50]
                ]
            ],
            "Text Style 1" => [
                "items" => [
                    "teamname" => ["x" => 1.85, "y" => 3.9, "rotate" => 0, "size" => 20]
                ]
            ],
            "Front Number" => [
                "items" => [
                    "front_number" => ["x" => 1.85, "y" => 3.0, "rotate" => 0, "size" => 20]
                ]
            ],
            "Upper Chest" => [
                "items" => [
                    "Upper_chest_flag" => ["x" => 1.9, "y" => 3.4, "rotate" => 0, "height" => 30, "width" => 30]
                ]
            ],
            "Crest" => [
                "items" => [
                    "Upper_chest_flag" => ["x" => 1.9, "y" => 3.4, "rotate" => 0, "height" => 30, "width" => 30]
                ]
            ],
            "Upper Chest Logo" => [
                "items" => [
                    "Upper_chest_flag" => ["x" => 1.85, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
            "Crest Logo" => [
                "items" => [
                    "front_crest_logo" => ["x" => 1.45, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
            "Shoulder Logo Left" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslf-3" => ["x" => 2.55, "y" => 5.6, "rotate" => 90 , "height" => 30, "width" => 30],
                    "lslf-2" => ["x" => 3.2, "y" => 2.1, "rotate" => 270, "height" => 30, "width" => 30] 
                ]
            ],
            "Shoulder Logo Right" => [
                "items" => [                    
                    "lslf-4" => ["x" => 1.43, "y" => 400, "rotate" => 270, "height" => 30, "width" => 30], // 👈 front sleeve
                    "lslf" => ["x" => 2.8, "y" => 1.52, "rotate" =>  90, "height" => 30, "width" => 30],
                ]
            ],
        ],
        78 => [            
            "Sleeve Number Left" => [
                "items" => [
                    "b_rightnumber" => ["x" => 5.95, "y" => 1.69, "rotate" => 90,"size" => 25],
                    "f_leftnumber" => ["x" => 2.65, "y" => 9.5, "rotate" => 90, "size" => 25]
                ]
            ],            
            "Sleeve Number Right" => [
                "items" => [
                    "b_leftnumber" => ["x" => 2, "y" => 1.69, "rotate" =>  270,"size" => 25],
                    "f_rightnumber" => ["x" => 1.4, "y" => 9.5, "rotate" => 270,"size" =>25] // 👈 front sleeve
                ]
            ],
            "Front Image" => [
                "items" => [
                    "front_logo" => ["x" => 2.03, "y" => 4.6, "rotate" => 0]
                ]
            ],
            "Front" => [
                "items" => [
                    "front_logo" => ["x" => 1.75, "y" => 3.4, "rotate" => 0, "height" => 80, "width" => 80]
                ]
            ],    
            "Front Logo" => [
                "items" => [
                    "front_logo" => ["x" => 1.75, "y" => 3.4, "rotate" => 0, "height" => 80, "width" => 80]
                ]
            ],    
            "Name Back" => [
                "items" => [
                    "player_name" => ["x" => 3, "y" => 1.48, "rotate" => 0, "size" => 25]
                ]
            ],            
            "Number Back" => [
                "items" => [
                    "player_number" => ["x" => 3.0, "y" => 1.29, "rotate" => 0, "size" => 60]
                ]
            ],
            "Right Shoulder" => [
                "items" => [
                    "lslf" => ["x" => 3.68, "y" => 3.3, "rotate" =>  270],
                    "lslf-2" => ["x" => 1.7, "y" => 56, "rotate" => 270] // 👈 front sleeve
                ]
            ],
            "Shoulder  Right" => [
                "items" => [
                    "lslf" => ["x" => 3.68, "y" => 3.3, "rotate" =>  270, "height" => 30, "width" => 30],
                    "lslf-4" => ["x" => 1.51, "y" => 400, "rotate" => 270, "height" => 30, "width" => 30] // 👈 front sleeve
                ]
            ],    
            "Left Shoulder" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslb" => ["x" => 5.35, "y" => 4.7, "rotate" => 90]
                ]
            ],
            "Shoulder  Left" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslf-3" => ["x" => 2.45, "y" => 5.4, "rotate" => 90 , "height" => 30, "width" => 30]
                ]
            ],    
            "Neck Patch" => [
                "items" => [
                    "l_shoulsadasder_logo_b" => ["x" => 2.2, "y" => 7.1, "rotate" => 90]
                ]
            ],
            "Logo" => [
                "items" => [
                    "teamname" => ["x" => 1.7, "y" => 3.0, "rotate" => 0, "height" => 50, "width" => 50]
                ]
            ],
            "Text Style 1" => [
                "items" => [
                    "teamname" => ["x" => 1.85, "y" => 3.9, "rotate" => 0, "size" => 20]
                ]
            ],
            "Front Number" => [
                "items" => [
                    "front_number" => ["x" => 1.85, "y" => 3.0, "rotate" => 0, "size" => 20]
                ]
            ],
            "Upper Chest" => [
                "items" => [
                    "Upper_chest_flag" => ["x" => 1.9, "y" => 3.4, "rotate" => 0, "height" => 30, "width" => 30]
                ]
            ],
            "Crest" => [
                "items" => [
                    "Upper_chest_flag" => ["x" => 1.9, "y" => 3.4, "rotate" => 0, "height" => 30, "width" => 30]
                ]
            ],
            "Upper Chest Logo" => [
                "items" => [
                    "Upper_chest_flag" => ["x" => 1.85, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
            "Crest Logo" => [
                "items" => [
                    "front_crest_logo" => ["x" => 1.45, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
            "Neck Logo" => [
                "items" => [
                    "front_crest_logo" => ["x" => 1.45, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
            "Shoulder Logo Left" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslf-3" => ["x" => 2.55, "y" => 5.6, "rotate" => 90 , "height" => 30, "width" => 30],
                    "lslf-2" => ["x" => 3.2, "y" => 2.1, "rotate" => 270, "height" => 30, "width" => 30] 
                ]
            ],
            "Shoulder Logo Right" => [
                "items" => [                    
                    "lslf-4" => ["x" => 1.43, "y" => 400, "rotate" => 270, "height" => 30, "width" => 30], // 👈 front sleeve
                    "lslf" => ["x" => 2.8, "y" => 1.52, "rotate" =>  90, "height" => 30, "width" => 30],
                ]
            ],
        ],
        87 => [
            "Left  Sleeve Number" => [
                "items" => [
                    "b_rightnumber" => ["x" => 6.2, "y" => 1.81, "rotate" => 90,"size" => 190],
                    "f_leftnumber" => ["x" => 2.8, "y" => 10, "rotate" => 90, "size" => 190]
                ]
            ],
            "Sleeves Number Left" => [
                "items" => [
                    "b_rightnumber" => ["x" => 5.95, "y" => 1.69, "rotate" => 90,"size" => 25],
                    "f_leftnumber" => ["x" => 2.65, "y" => 9.5, "rotate" => 90, "size" => 25]
                ]
            ],
            "Right Sleeve Number" => [
                "items" => [
                    "b_leftnumber" => ["x" => 2.05, "y" => 1.81, "rotate" =>  270,"size" => 190],
                    "f_rightnumber" => ["x" => 1.46, "y" => 10, "rotate" => 270,"size" => 190] // 👈 front sleeve
                ]
            ],
            "Sleeve Number Right" => [
                "items" => [
                    "b_leftnumber" => ["x" => 2, "y" => 1.69, "rotate" =>  270,"size" => 25],
                    "f_rightnumber" => ["x" => 1.4, "y" => 9.5, "rotate" => 270,"size" =>25] // 👈 front sleeve
                ]
            ],
            "Front Image" => [
                "items" => [
                    "front_logo" => ["x" => 2.03, "y" => 4.6, "rotate" => 0]
                ]
            ],
            "Front" => [
                "items" => [
                    "front_logo" => ["x" => 1.75, "y" => 3.4, "rotate" => 0, "height" => 80, "width" => 80]
                ]
            ],    
            "Front Logo" => [
                "items" => [
                    "front_logo" => ["x" => 1.75, "y" => 3.4, "rotate" => 0, "height" => 80, "width" => 80]
                ]
            ],    
            "Back Name" => [
                "items" => [
                    "player_name" => ["x" => 3.1, "y" => 1.58, "rotate" => 0, "size" => 160]
                ]
            ],
            "Name Back" => [
                "items" => [
                    "player_name" => ["x" => 3.0, "y" => 1.48, "rotate" => 0, "size" => 20]
                ]
            ],
            "Back Number" => [
                "items" => [
                    "player_number" => ["x" => 3.1, "y" => 1.36, "rotate" => 0, "size" => 150]
                ]
            ],
            "Number Back" => [
                "items" => [
                    "player_number" => ["x" => 3.0, "y" => 1.29, "rotate" => 0, "size" => 50]
                ]
            ],
            "Right Shoulder" => [
                "items" => [
                    "lslf" => ["x" => 3.68, "y" => 3.3, "rotate" =>  270],
                    "lslf-2" => ["x" => 1.7, "y" => 56, "rotate" => 270] // 👈 front sleeve
                ]
            ],
            "Shoulder  Right" => [
                "items" => [
                    "lslf" => ["x" => 3.68, "y" => 3.3, "rotate" =>  270, "height" => 30, "width" => 30],
                    "lslf-4" => ["x" => 1.51, "y" => 400, "rotate" => 270, "height" => 30, "width" => 30] // 👈 front sleeve
                ]
            ],    
            "Left Shoulder" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslb" => ["x" => 5.35, "y" => 4.7, "rotate" => 90]
                ]
            ],
            "Shoulder  Left" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslf-3" => ["x" => 2.45, "y" => 5.4, "rotate" => 90 , "height" => 30, "width" => 30]
                ]
            ],    
            "Neck Patch" => [
                "items" => [
                    "l_shoulsadasder_logo_b" => ["x" => 2.2, "y" => 7.1, "rotate" => 90]
                ]
            ],
            "Logo" => [
                "items" => [
                    "teamname" => ["x" => 1.7, "y" => 3.0, "rotate" => 0, "height" => 50, "width" => 50]
                ]
            ],
            "Text Style 1" => [
                "items" => [
                    "teamname" => ["x" => 1.85, "y" => 3.9, "rotate" => 0, "size" => 20]
                ]
            ],
            "Front Number" => [
                "items" => [
                    "front_number" => ["x" => 1.85, "y" => 3.0, "rotate" => 0, "size" => 20]
                ]
            ],
            "Upper Chest" => [
                "items" => [
                    "Upper_chest_flag" => ["x" => 1.9, "y" => 3.4, "rotate" => 0, "height" => 30, "width" => 30]
                ]
            ],
            "Upper Chest Logo" => [
                "items" => [
                    "Upper_chest_flag" => ["x" => 1.85, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
            "Crest Logo" => [
                "items" => [
                    "front_crest_logo" => ["x" => 1.45, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
            "Shoulder Logo Left" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslf-3" => ["x" => 2.45, "y" => 5.4, "rotate" => 90 , "height" => 30, "width" => 30]
                ]
            ],
            "Shoulder Logo Right" => [
                "items" => [
                    "lslf" => ["x" => 3.68, "y" => 3.3, "rotate" =>  270, "height" => 30, "width" => 30],
                    "lslf-4" => ["x" => 1.45, "y" => 400, "rotate" => 270, "height" => 30, "width" => 30] // 👈 front sleeve
                ]
            ]
        ],
        91 => [            
            "Sleeve Number Left" => [
                "items" => [
                    "b_rightnumber" => ["x" => 5.95, "y" => 1.7, "rotate" => 90,"size" => 25],
                    "f_leftnumber" => ["x" => 2.6, "y" => 9.5, "rotate" => 90, "size" => 25]
                ]
            ],            
            "Sleeve Number Right" => [
                "items" => [
                    "b_leftnumber" => ["x" => 2, "y" => 1.70, "rotate" =>  270,"size" => 25],
                    "f_rightnumber" => ["x" => 1.41, "y" => 9.5, "rotate" => 270,"size" =>25] // 👈 front sleeve
                ]
            ],
            "Front Image" => [
                "items" => [
                    "front_logo" => ["x" => 2.03, "y" => 4.6, "rotate" => 0]
                ]
            ],
            "Front" => [
                "items" => [
                    "front_logo" => ["x" => 1.75, "y" => 3.4, "rotate" => 0, "height" => 80, "width" => 80]
                ]
            ],    
            "Front Logo" => [
                "items" => [
                    "front_logo" => ["x" => 1.75, "y" => 3.4, "rotate" => 0, "height" => 80, "width" => 80]
                ]
            ],    
            "Back Name" => [
                "items" => [
                    "player_name" => ["x" => 3.1, "y" => 1.58, "rotate" => 0, "size" => 160]
                ]
            ],
            "Name Back" => [
                "items" => [
                    "player_name" => ["x" => 3.0, "y" => 1.48, "rotate" => 0, "size" => 20]
                ]
            ],
            "Back Number" => [
                "items" => [
                    "player_number" => ["x" => 3.1, "y" => 1.36, "rotate" => 0, "size" => 150]
                ]
            ],
            "Number Back" => [
                "items" => [
                    "player_number" => ["x" => 3.0, "y" => 1.29, "rotate" => 0, "size" => 50]
                ]
            ],
            "Right Shoulder" => [
                "items" => [
                    "lslf" => ["x" => 3.68, "y" => 3.3, "rotate" =>  270],
                    "lslf-2" => ["x" => 1.7, "y" => 56, "rotate" => 270] // 👈 front sleeve
                ]
            ],
            "Shoulder  Right" => [
                "items" => [
                    "lslf" => ["x" => 3.68, "y" => 3.3, "rotate" =>  270, "height" => 30, "width" => 30],
                    "lslf-4" => ["x" => 1.51, "y" => 400, "rotate" => 270, "height" => 30, "width" => 30] // 👈 front sleeve
                ]
            ],    
            "Left Shoulder" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslb" => ["x" => 5.35, "y" => 4.7, "rotate" => 90]
                ]
            ],
            "Shoulder  Left" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslf-3" => ["x" => 2.55, "y" => 5.4, "rotate" => 90 , "height" => 30, "width" => 30]
                ]
            ],    
            "Neck Patch" => [
                "items" => [
                    "l_shoulsadasder_logo_b" => ["x" => 2.2, "y" => 7.1, "rotate" => 90]
                ]
            ],
            "Logo" => [
                "items" => [
                    "teamname" => ["x" => 1.7, "y" => 3.0, "rotate" => 0, "height" => 50, "width" => 50]
                ]
            ],
            "Text Style 1" => [
                "items" => [
                    "teamname" => ["x" => 1.85, "y" => 3.9, "rotate" => 0, "size" => 20]
                ]
            ],
            "Front Number" => [
                "items" => [
                    "front_number" => ["x" => 1.85, "y" => 3.0, "rotate" => 0, "size" => 20]
                ]
            ],
            "Upper Chest" => [
                "items" => [
                    "Upper_chest_flag" => ["x" => 1.9, "y" => 3.4, "rotate" => 0, "height" => 30, "width" => 30]
                ]
            ],
            "Upper Chest Logo" => [
                "items" => [
                    "Upper_chest_flag" => ["x" => 1.85, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
            "Crest Logo" => [
                "items" => [
                    "front_crest_logo" => ["x" => 1.45, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
            "Shoulder Logo Left" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslf-3" => ["x" => 2.5, "y" => 5.8, "rotate" => 90 , "height" => 30, "width" => 30],
                     "lslf-2" => ["x" => 3.2, "y" => 2.1, "rotate" => 270, "height" => 30, "width" => 30] // 👈 front sleeve
                ]
            ],
            "Shoulder Logo Right" => [
                "items" => [
                    "lslf" => ["x" => 3.68, "y" => 3.3, "rotate" =>  270, "height" => 30, "width" => 30],
                    "lslf-4" => ["x" => 1.45, "y" => 800, "rotate" => 270, "height" => 30, "width" => 30],
                    "lslf" => ["x" => 2.8, "y" => 1.52, "rotate" =>  90, "height" => 30, "width" => 30],
                ]
            ]
        ],
        90 => [            
            "Sleeve Number Left" => [
                "items" => [
                    "b_rightnumber" => ["x" => 5.95, "y" => 1.7, "rotate" => 90,"size" => 25],
                    "f_leftnumber" => ["x" => 2.6, "y" => 9.5, "rotate" => 90, "size" => 25]
                ]
            ],            
            "Sleeve Number Right" => [
                "items" => [
                    "b_leftnumber" => ["x" => 2, "y" => 1.70, "rotate" =>  270,"size" => 25],
                    "f_rightnumber" => ["x" => 1.41, "y" => 9.5, "rotate" => 270,"size" =>25] // 👈 front sleeve
                ]
            ],
            "Front Image" => [
                "items" => [
                    "front_logo" => ["x" => 2.03, "y" => 4.6, "rotate" => 0]
                ]
            ],
            "Front" => [
                "items" => [
                    "front_logo" => ["x" => 1.75, "y" => 3.4, "rotate" => 0, "height" => 80, "width" => 80]
                ]
            ],    
            "Front Logo" => [
                "items" => [
                    "front_logo" => ["x" => 1.75, "y" => 3.4, "rotate" => 0, "height" => 80, "width" => 80]
                ]
            ],    
            "Back Name" => [
                "items" => [
                    "player_name" => ["x" => 3.1, "y" => 1.58, "rotate" => 0, "size" => 160]
                ]
            ],
            "Name Back" => [
                "items" => [
                    "player_name" => ["x" => 3.0, "y" => 1.48, "rotate" => 0, "size" => 20]
                ]
            ],
            "Back Number" => [
                "items" => [
                    "player_number" => ["x" => 3.1, "y" => 1.36, "rotate" => 0, "size" => 150]
                ]
            ],
            "Number Back" => [
                "items" => [
                    "player_number" => ["x" => 3.0, "y" => 1.29, "rotate" => 0, "size" => 50]
                ]
            ],
            "Right Shoulder" => [
                "items" => [
                    "lslf" => ["x" => 3.68, "y" => 3.3, "rotate" =>  270],
                    "lslf-2" => ["x" => 1.7, "y" => 56, "rotate" => 270] // 👈 front sleeve
                ]
            ],
            "Shoulder  Right" => [
                "items" => [
                    "lslf" => ["x" => 3.68, "y" => 3.3, "rotate" =>  270, "height" => 30, "width" => 30],
                    "lslf-4" => ["x" => 1.51, "y" => 400, "rotate" => 270, "height" => 30, "width" => 30] // 👈 front sleeve
                ]
            ],    
            "Left Shoulder" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslb" => ["x" => 5.35, "y" => 4.7, "rotate" => 90]
                ]
            ],
            "Shoulder  Left" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslf-3" => ["x" => 2.55, "y" => 5.4, "rotate" => 90 , "height" => 30, "width" => 30]
                ]
            ],    
            "Neck Patch" => [
                "items" => [
                    "l_shoulsadasder_logo_b" => ["x" => 2.2, "y" => 7.1, "rotate" => 90]
                ]
            ],
            "Logo" => [
                "items" => [
                    "teamname" => ["x" => 1.7, "y" => 3.0, "rotate" => 0, "height" => 50, "width" => 50]
                ]
            ],
            "Text Style 1" => [
                "items" => [
                    "teamname" => ["x" => 1.85, "y" => 3.9, "rotate" => 0, "size" => 20]
                ]
            ],
            "Front Number" => [
                "items" => [
                    "front_number" => ["x" => 1.85, "y" => 3.0, "rotate" => 0, "size" => 20]
                ]
            ],
            "Upper Chest" => [
                "items" => [
                    "Upper_chest_flag" => ["x" => 1.9, "y" => 3.4, "rotate" => 0, "height" => 30, "width" => 30]
                ]
            ],
            "Upper Chest Logo" => [
                "items" => [
                    "Upper_chest_flag" => ["x" => 1.85, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
            "Crest Logo" => [
                "items" => [
                    "front_crest_logo" => ["x" => 1.45, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
            "Shoulder Logo Left" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslf-3" => ["x" => 2.5, "y" => 5.8, "rotate" => 90 , "height" => 30, "width" => 30],
                     "lslf-2" => ["x" => 3.2, "y" => 2.1, "rotate" => 270, "height" => 30, "width" => 30] // 👈 front sleeve
                ]
            ],
            "Shoulder Logo Right" => [
                "items" => [
                    "lslf" => ["x" => 3.68, "y" => 3.3, "rotate" =>  270, "height" => 30, "width" => 30],
                    "lslf-4" => ["x" => 1.45, "y" => 800, "rotate" => 270, "height" => 30, "width" => 30],
                    "lslf" => ["x" => 2.8, "y" => 1.52, "rotate" =>  90, "height" => 30, "width" => 30],
                ]
            ]
        ],               
        97 => [            
            "Sleeve Number Left" => [
                "items" => [
                    "b_rightnumber" => ["x" => 5.95, "y" => 1.7, "rotate" => 90,"size" => 25],
                    "f_leftnumber" => ["x" => 2.6, "y" => 9.5, "rotate" => 90, "size" => 25]
                ]
            ],            
            "Sleeve Number Right" => [
                "items" => [
                    "b_leftnumber" => ["x" => 2, "y" => 1.70, "rotate" =>  270,"size" => 25],
                    "f_rightnumber" => ["x" => 1.41, "y" => 9.5, "rotate" => 270,"size" =>25] // 👈 front sleeve
                ]
            ],
            "Front Image" => [
                "items" => [
                    "front_logo" => ["x" => 2.03, "y" => 4.6, "rotate" => 0]
                ]
            ],
            "Front" => [
                "items" => [
                    "front_logo" => ["x" => 1.75, "y" => 3.4, "rotate" => 0, "height" => 80, "width" => 80]
                ]
            ],    
            "Front Logo" => [
                "items" => [
                    "front_logo" => ["x" => 1.75, "y" => 3.4, "rotate" => 0, "height" => 80, "width" => 80]
                ]
            ],    
            "Back Name" => [
                "items" => [
                    "player_name" => ["x" => 3.1, "y" => 1.58, "rotate" => 0, "size" => 160]
                ]
            ],
            "Name Back" => [
                "items" => [
                    "player_name" => ["x" => 3.0, "y" => 1.48, "rotate" => 0, "size" => 20]
                ]
            ],
            "Back Number" => [
                "items" => [
                    "player_number" => ["x" => 3.1, "y" => 1.36, "rotate" => 0, "size" => 150]
                ]
            ],
            "Number Back" => [
                "items" => [
                    "player_number" => ["x" => 3.0, "y" => 1.29, "rotate" => 0, "size" => 50]
                ]
            ],
            "Right Shoulder" => [
                "items" => [
                    "lslf" => ["x" => 3.68, "y" => 3.3, "rotate" =>  270],
                    "lslf-2" => ["x" => 1.7, "y" => 56, "rotate" => 270] // 👈 front sleeve
                ]
            ],
            "Shoulder  Right" => [
                "items" => [
                    "lslf" => ["x" => 3.68, "y" => 3.3, "rotate" =>  270, "height" => 30, "width" => 30],
                    "lslf-4" => ["x" => 1.51, "y" => 400, "rotate" => 270, "height" => 30, "width" => 30] // 👈 front sleeve
                ]
            ],    
            "Left Shoulder" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslb" => ["x" => 5.35, "y" => 4.7, "rotate" => 90]
                ]
            ],
            "Shoulder  Left" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslf-3" => ["x" => 2.55, "y" => 5.4, "rotate" => 90 , "height" => 30, "width" => 30]
                ]
            ],    
            "Neck Patch" => [
                "items" => [
                    "l_shoulsadasder_logo_b" => ["x" => 2.2, "y" => 7.1, "rotate" => 90]
                ]
            ],
            "Logo" => [
                "items" => [
                    "teamname" => ["x" => 1.7, "y" => 3.0, "rotate" => 0, "height" => 50, "width" => 50]
                ]
            ],
            "Text Style 1" => [
                "items" => [
                    "teamname" => ["x" => 1.85, "y" => 3.9, "rotate" => 0, "size" => 20]
                ]
            ],
            "Front Number" => [
                "items" => [
                    "front_number" => ["x" => 1.85, "y" => 3.0, "rotate" => 0, "size" => 20]
                ]
            ],
            "Upper Chest" => [
                "items" => [
                    "Upper_chest_flag" => ["x" => 1.9, "y" => 3.4, "rotate" => 0, "height" => 30, "width" => 30]
                ]
            ],
            "Upper Chest Logo" => [
                "items" => [
                    "Upper_chest_flag" => ["x" => 1.85, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
            "Crest Logo" => [
                "items" => [
                    "front_crest_logo" => ["x" => 1.45, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
            "Shoulder Logo Left" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslf-3" => ["x" => 2.5, "y" => 5.8, "rotate" => 90 , "height" => 30, "width" => 30],
                     "lslf-2" => ["x" => 3.2, "y" => 2.1, "rotate" => 270, "height" => 30, "width" => 30] // 👈 front sleeve
                ]
            ],
            "Shoulder Logo Right" => [
                "items" => [
                    "lslf" => ["x" => 3.68, "y" => 3.3, "rotate" =>  270, "height" => 30, "width" => 30],
                    "lslf-4" => ["x" => 1.45, "y" => 800, "rotate" => 270, "height" => 30, "width" => 30],
                    "lslf" => ["x" => 2.8, "y" => 1.52, "rotate" =>  90, "height" => 30, "width" => 30],
                ]
            ]
        ],               
        112 => [
            "Left  Sleeve Number" => [
                "items" => [
                    "b_rightnumber" => ["x" => 6.2, "y" => 1.81, "rotate" => 90,"size" => 190],
                    "f_leftnumber" => ["x" => 2.8, "y" => 10, "rotate" => 90, "size" => 190]
                ]
            ],
            "Sleeve Number Left" => [
                "items" => [
                    "b_rightnumber" => ["x" => 5.5, "y" => 1.66, "rotate" => 90,"size" => 30],
                    "f_leftnumber" => ["x" => 2.5, "y" => 9, "rotate" => 90, "size" => 30]
                ]
            ],
            "Right Sleeve Number" => [
                "items" => [
                    "b_leftnumber" => ["x" => 2.05, "y" => 1.81, "rotate" =>  270,"size" => 190],
                    "f_rightnumber" => ["x" => 1.46, "y" => 10, "rotate" => 270,"size" => 190] // 👈 front sleeve
                ]
            ],
            "Sleeve Number Right" => [
                "items" => [
                    "b_leftnumber" => ["x" => 2.18, "y" => 1.66, "rotate" =>  270,"size" => 30],
                    "f_rightnumber" => ["x" => 1.46, "y" => 9, "rotate" => 270,"size" =>30] // 👈 front sleeve
                ]
            ],
            "Front Image" => [
                "items" => [
                    "front_logo" => ["x" => 2.03, "y" => 4.6, "rotate" => 0]
                ]
            ],
            "Front" => [
                "items" => [
                    "front_logo" => ["x" => 1.75, "y" => 3.4, "rotate" => 0, "height" => 80, "width" => 80]
                ]
            ],    
            "Front Logo" => [
                "items" => [
                    "front_logo" => ["x" => 1.75, "y" => 3.4, "rotate" => 0, "height" => 80, "width" => 80]
                ]
            ],    
            "Back Name" => [
                "items" => [
                    "player_name" => ["x" => 3.1, "y" => 1.58, "rotate" => 0, "size" => 160]
                ]
            ],
            "Name Back" => [
                "items" => [
                    "player_name" => ["x" => 3.1, "y" => 1.48, "rotate" => 0, "size" => 20]
                ]
            ],
            "Back Number" => [
                "items" => [
                    "player_number" => ["x" => 3.1, "y" => 1.36, "rotate" => 0, "size" => 150]
                ]
            ],
            "Number Back" => [
                "items" => [
                    "player_number" => ["x" => 3.1, "y" => 1.26, "rotate" => 0, "size" => 70]
                ]
            ],                
            "Neck Patch" => [
                "items" => [
                    "l_shoulsadasder_logo_b" => ["x" => 2.2, "y" => 7.1, "rotate" => 90]
                ]
            ],
            "Logo" => [
                "items" => [
                    "teamname" => ["x" => 1.7, "y" => 3.0, "rotate" => 0, "height" => 50, "width" => 50]
                ]
            ],
            "Text Style 1" => [
                "items" => [
                    "teamname" => ["x" => 1.85, "y" => 3.9, "rotate" => 0, "size" => 20]
                ]
            ],
            "Front Number" => [
                "items" => [
                    "front_number" => ["x" => 1.85, "y" => 3.0, "rotate" => 0, "size" => 20]
                ]
            ],
            "Upper Chest" => [
                "items" => [
                    "Upper_chest_flag" => ["x" => 1.9, "y" => 3.4, "rotate" => 0, "height" => 30, "width" => 30]
                ]
            ],
            "Upper Chest Logo" => [
                "items" => [
                    "Upper_chest_flag" => ["x" => 1.85, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
            "Crest Logo" => [
                "items" => [
                    "front_crest_logo" => ["x" => 1.45, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
            "Shoulder Logo Left" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslf-3" => ["x" => 2.55, "y" => 5.4, "rotate" => 90 , "height" => 30, "width" => 30],
                    "lslf-2" => ["x" => 3.2, "y" => 2.05, "rotate" => 270, "height" => 30, "width" => 30] // 👈 front sleeve
                ]
            ],
            "Shoulder Logo Right" => [
                "items" => [
                    "lslf" => ["x" => 3, "y" => 1.48, "rotate" =>  90, "height" => 30, "width" => 30],
                    "lslf-4" => ["x" => 1.45, "y" => 400, "rotate" => 270, "height" => 30, "width" => 30] // 👈 front sleeve
                ]
            ],
            // 👉 add default map here
        ],
        111 => [
            "Left  Sleeve Number" => [
                "items" => [
                    "b_rightnumber" => ["x" => 6.2, "y" => 1.81, "rotate" => 90,"size" => 190],
                    "f_leftnumber" => ["x" => 2.8, "y" => 10, "rotate" => 90, "size" => 190]
                ]
            ],
            "Sleeve Left" => [
                "items" => [
                    "b_rightnumber" => ["x" => 5.9, "y" => 1.66, "rotate" => 90,"size" => 30],
                    "f_leftnumber" => ["x" => 2.55, "y" => 9, "rotate" => 90, "size" => 30]
                ]
            ],
            "Right Sleeve Number" => [
                "items" => [
                    "b_leftnumber" => ["x" => 2.05, "y" => 1.81, "rotate" =>  270,"size" => 190],
                    "f_rightnumber" => ["x" => 1.46, "y" => 10, "rotate" => 270,"size" => 190] // 👈 front sleeve
                ]
            ],
            "Sleeve Right" => [
                "items" => [
                    "b_leftnumber" => ["x" => 2.13, "y" => 1.66, "rotate" =>  270,"size" => 30],
                    "f_rightnumber" => ["x" => 1.46, "y" => 9, "rotate" => 270,"size" =>30] // 👈 front sleeve
                ]
            ],
            "Front Image" => [
                "items" => [
                    "front_logo" => ["x" => 2.03, "y" => 4.6, "rotate" => 0]
                ]
            ],
            "Front" => [
                "items" => [
                    "front_logo" => ["x" => 1.75, "y" => 3.4, "rotate" => 0, "height" => 80, "width" => 80]
                ]
            ],    
            "Front Logo" => [
                "items" => [
                    "front_logo" => ["x" => 1.75, "y" => 3.4, "rotate" => 0, "height" => 80, "width" => 80]
                ]
            ],    
            "Back Name" => [
                "items" => [
                    "player_name" => ["x" => 3.1, "y" => 1.46, "rotate" => 0, "size" => 20]
                ]
            ],
            "Name Back" => [
                "items" => [
                    "player_name" => ["x" => 3.1, "y" => 1.48, "rotate" => 0, "size" => 20]
                ]
            ],
            "Back Number" => [
                "items" => [
                    "player_number" => ["x" => 3.1, "y" => 1.36, "rotate" => 0, "size" => 150]
                ]
            ],
            "Number Back" => [
                "items" => [
                    "player_number" => ["x" => 3, "y" => 1.26, "rotate" => 0, "size" => 45]
                ]
            ],                
            "Neck Patch" => [
                "items" => [
                    "l_shoulsadasder_logo_b" => ["x" => 2.2, "y" => 7.1, "rotate" => 90]
                ]
            ],
            "Logo" => [
                "items" => [
                    "teamname" => ["x" => 1.7, "y" => 3.0, "rotate" => 0, "height" => 50, "width" => 50]
                ]
            ],
            "Text Style 1" => [
                "items" => [
                    "teamname" => ["x" => 1.85, "y" => 3.9, "rotate" => 0, "size" => 20]
                ]
            ],
            "Front Number" => [
                "items" => [
                    "front_number" => ["x" => 1.85, "y" => 3.0, "rotate" => 0, "size" => 20]
                ]
            ],
            "Upper Chest Logo Right" => [
                "items" => [
                    "Upper_chest_flag" => ["x" => 1.85, "y" => 3.5, "rotate" => 0, "height" => 30, "width" => 20]
                ]
            ],
            "Upper Chest Logo Left" => [
                "items" => [
                    "Upper_chest_flag" => ["x" => 1.85, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
            "Crest" => [
                "items" => [
                    "front_crest_logo" => ["x" => 1.45, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
            "Shoulder Logo Left" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslf-3" => ["x" => 2.55, "y" => 5.4, "rotate" => 90 , "height" => 30, "width" => 30],
                    "lslf-2" => ["x" => 3.2, "y" => 2.05, "rotate" => 270, "height" => 30, "width" => 30] // 👈 front sleeve
                ]
            ],
            "Shoulder Logo Right" => [
                "items" => [
                    "lslf" => ["x" => 3, "y" => 1.48, "rotate" =>  90, "height" => 30, "width" => 30],
                    "lslf-4" => ["x" => 1.45, "y" => 400, "rotate" => 270, "height" => 30, "width" => 30] // 👈 front sleeve
                ]
            ],
            // 👉 add default map here
        ],
        108 => [
            "Left  Sleeve Number" => [
                "items" => [
                    "b_rightnumber" => ["x" => 6.2, "y" => 1.81, "rotate" => 90,"size" => 190],
                    "f_leftnumber" => ["x" => 2.8, "y" => 10, "rotate" => 90, "size" => 190]
                ]
            ],
            "Sleeve Number Left" => [
                "items" => [
                    "b_rightnumber" => ["x" => 5.9, "y" => 1.66, "rotate" => 90,"size" => 30],
                    "f_leftnumber" => ["x" => 2.55, "y" => 9, "rotate" => 90, "size" => 30]
                ]
            ],
            "Right Sleeve Number" => [
                "items" => [
                    "b_leftnumber" => ["x" => 2.05, "y" => 1.81, "rotate" =>  270,"size" => 190],
                    "f_rightnumber" => ["x" => 1.46, "y" => 10, "rotate" => 270,"size" => 190] // 👈 front sleeve
                ]
            ],
            "Sleeve Number Right" => [
                "items" => [
                    "b_leftnumber" => ["x" => 2.13, "y" => 1.66, "rotate" =>  270,"size" => 30],
                    "f_rightnumber" => ["x" => 1.46, "y" => 9, "rotate" => 270,"size" =>30] // 👈 front sleeve
                ]
            ],
            "Front Image" => [
                "items" => [
                    "front_logo" => ["x" => 2.03, "y" => 4.6, "rotate" => 0]
                ]
            ],
            "Front" => [
                "items" => [
                    "front_logo" => ["x" => 1.75, "y" => 3.4, "rotate" => 0, "height" => 80, "width" => 80]
                ]
            ],    
            "Front Logo" => [
                "items" => [
                    "front_logo" => ["x" => 1.75, "y" => 3.4, "rotate" => 0, "height" => 80, "width" => 80]
                ]
            ],    
            "Back Name" => [
                "items" => [
                    "player_name" => ["x" => 3.1, "y" => 1.46, "rotate" => 0, "size" => 20]
                ]
            ],
            "Name Back" => [
                "items" => [
                    "player_name" => ["x" => 3.1, "y" => 1.48, "rotate" => 0, "size" => 20]
                ]
            ],
            "Back Number" => [
                "items" => [
                    "player_number" => ["x" => 3.1, "y" => 1.36, "rotate" => 0, "size" => 150]
                ]
            ],
            "Number Back" => [
                "items" => [
                    "player_number" => ["x" => 3, "y" => 1.26, "rotate" => 0, "size" => 45]
                ]
            ],                
            "Neck Patch" => [
                "items" => [
                    "l_shoulsadasder_logo_b" => ["x" => 2.2, "y" => 7.1, "rotate" => 90]
                ]
            ],
            "Logo" => [
                "items" => [
                    "teamname" => ["x" => 1.7, "y" => 3.0, "rotate" => 0, "height" => 50, "width" => 50]
                ]
            ],
            "Text Style 1" => [
                "items" => [
                    "teamname" => ["x" => 1.85, "y" => 3.9, "rotate" => 0, "size" => 20]
                ]
            ],
            "Front Number" => [
                "items" => [
                    "front_number" => ["x" => 1.85, "y" => 3.0, "rotate" => 0, "size" => 20]
                ]
            ],
            "Upper Chest" => [
                "items" => [
                    "Upper_chest_flag" => ["x" => 1.85, "y" => 3.5, "rotate" => 0, "height" => 30, "width" => 20]
                ]
            ],
            "Upper Chest Logo" => [
                "items" => [
                    "Upper_chest_flag" => ["x" => 1.85, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
            "Crest" => [
                "items" => [
                    "front_crest_logo" => ["x" => 1.45, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
            "Shoulder Logo Left" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslf-3" => ["x" => 2.55, "y" => 5.4, "rotate" => 90 , "height" => 30, "width" => 30],
                    "lslf-2" => ["x" => 3.2, "y" => 2.05, "rotate" => 270, "height" => 30, "width" => 30] // 👈 front sleeve
                ]
            ],
            "Shoulder Logo Right" => [
                "items" => [
                    "lslf" => ["x" => 3, "y" => 1.48, "rotate" =>  90, "height" => 30, "width" => 30],
                    "lslf-4" => ["x" => 1.45, "y" => 400, "rotate" => 270, "height" => 30, "width" => 30] // 👈 front sleeve
                ]
            ],
            // 👉 add default map here
        ],
        99 => [
            "Left  Sleeve Number" => [
                "items" => [
                    "b_rightnumber" => ["x" => 6.2, "y" => 1.81, "rotate" => 90,"size" => 190],
                    "f_leftnumber" => ["x" => 2.8, "y" => 10, "rotate" => 90, "size" => 190]
                ]
            ],
            "Sleeve Number Left" => [
                "items" => [
                    "b_rightnumber" => ["x" => 5.9, "y" => 1.66, "rotate" => 90,"size" => 30],
                    "f_leftnumber" => ["x" => 2.55, "y" => 9, "rotate" => 90, "size" => 30]
                ]
            ],
            "Right Sleeve Number" => [
                "items" => [
                    "b_leftnumber" => ["x" => 2.05, "y" => 1.81, "rotate" =>  270,"size" => 190],
                    "f_rightnumber" => ["x" => 1.46, "y" => 10, "rotate" => 270,"size" => 190] // 👈 front sleeve
                ]
            ],
            "Sleeve Number Right" => [
                "items" => [
                    "b_leftnumber" => ["x" => 2.13, "y" => 1.66, "rotate" =>  270,"size" => 30],
                    "f_rightnumber" => ["x" => 1.46, "y" => 9, "rotate" => 270,"size" =>30] // 👈 front sleeve
                ]
            ],
            "Front Image" => [
                "items" => [
                    "front_logo" => ["x" => 2.03, "y" => 4.6, "rotate" => 0]
                ]
            ],
            "Front" => [
                "items" => [
                    "front_logo" => ["x" => 1.75, "y" => 3.4, "rotate" => 0, "height" => 80, "width" => 80]
                ]
            ],    
            "Front Logo" => [
                "items" => [
                    "front_logo" => ["x" => 1.75, "y" => 3.4, "rotate" => 0, "height" => 80, "width" => 80]
                ]
            ],    
            "Back Name" => [
                "items" => [
                    "player_name" => ["x" => 3.1, "y" => 1.46, "rotate" => 0, "size" => 20]
                ]
            ],
            "Name Back" => [
                "items" => [
                    "player_name" => ["x" => 3.1, "y" => 1.48, "rotate" => 0, "size" => 20]
                ]
            ],
            "Back Number" => [
                "items" => [
                    "player_number" => ["x" => 3.1, "y" => 1.36, "rotate" => 0, "size" => 150]
                ]
            ],
            "Number Back" => [
                "items" => [
                    "player_number" => ["x" => 3.1, "y" => 1.26, "rotate" => 0, "size" => 70]
                ]
            ],                
            "Neck Patch" => [
                "items" => [
                    "l_shoulsadasder_logo_b" => ["x" => 2.2, "y" => 7.1, "rotate" => 90]
                ]
            ],
            "Logo" => [
                "items" => [
                    "teamname" => ["x" => 1.7, "y" => 3.0, "rotate" => 0, "height" => 50, "width" => 50]
                ]
            ],
            "Text Style 1" => [
                "items" => [
                    "teamname" => ["x" => 1.85, "y" => 3.9, "rotate" => 0, "size" => 20]
                ]
            ],
            "Front Number" => [
                "items" => [
                    "front_number" => ["x" => 1.85, "y" => 3.0, "rotate" => 0, "size" => 20]
                ]
            ],
            "Upper Chest" => [
                "items" => [
                    "Upper_chest_flag" => ["x" => 1.85, "y" => 3.5, "rotate" => 0, "height" => 30, "width" => 20]
                ]
            ],
            "Upper Chest Logo" => [
                "items" => [
                    "Upper_chest_flag" => ["x" => 1.85, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
            "Crest" => [
                "items" => [
                    "front_crest_logo" => ["x" => 1.45, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
            "Crest Logo" => [
                "items" => [
                    "front_crest_logo" => ["x" => 1.45, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
            "Shoulder Logo Left" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslf-3" => ["x" => 2.55, "y" => 5.4, "rotate" => 90 , "height" => 30, "width" => 30],
                    "lslf-2" => ["x" => 3.2, "y" => 2.05, "rotate" => 270, "height" => 30, "width" => 30] // 👈 front sleeve
                ]
            ],
            "Shoulder Logo Right" => [
                "items" => [
                    "lslf" => ["x" => 3, "y" => 1.48, "rotate" =>  90, "height" => 30, "width" => 30],
                    "lslf-4" => ["x" => 1.45, "y" => 400, "rotate" => 270, "height" => 30, "width" => 30] // 👈 front sleeve
                ]
            ],
            // 👉 add default map here
        ],
        92 => [
            "Left  Sleeve Number" => [
                "items" => [
                    "b_rightnumber" => ["x" => 6.2, "y" => 1.81, "rotate" => 90,"size" => 190],
                    "f_leftnumber" => ["x" => 2.8, "y" => 10, "rotate" => 90, "size" => 190]
                ]
            ],
            "Sleeve Number Left" => [
                "items" => [
                    "b_rightnumber" => ["x" => 5.9, "y" => 1.66, "rotate" => 90,"size" => 30],
                    "f_leftnumber" => ["x" => 2.55, "y" => 9, "rotate" => 90, "size" => 30]
                ]
            ],
            "Right Sleeve Number" => [
                "items" => [
                    "b_leftnumber" => ["x" => 2.05, "y" => 1.81, "rotate" =>  270,"size" => 190],
                    "f_rightnumber" => ["x" => 1.46, "y" => 10, "rotate" => 270,"size" => 190] // 👈 front sleeve
                ]
            ],
            "Sleeve Number Right" => [
                "items" => [
                    "b_leftnumber" => ["x" => 2.13, "y" => 1.66, "rotate" =>  270,"size" => 30],
                    "f_rightnumber" => ["x" => 1.46, "y" => 9, "rotate" => 270,"size" =>30] // 👈 front sleeve
                ]
            ],
            "Front Image" => [
                "items" => [
                    "front_logo" => ["x" => 2.03, "y" => 4.6, "rotate" => 0]
                ]
            ],
            "Front" => [
                "items" => [
                    "front_logo" => ["x" => 1.75, "y" => 3.4, "rotate" => 0, "height" => 80, "width" => 80]
                ]
            ],    
            "Front Logo" => [
                "items" => [
                    "front_logo" => ["x" => 1.75, "y" => 3.4, "rotate" => 0, "height" => 80, "width" => 80]
                ]
            ],    
            "Back Name" => [
                "items" => [
                    "player_name" => ["x" => 3.1, "y" => 1.46, "rotate" => 0, "size" => 20]
                ]
            ],
            "Name Back" => [
                "items" => [
                    "player_name" => ["x" => 3.1, "y" => 1.48, "rotate" => 0, "size" => 20]
                ]
            ],
            "Back Number" => [
                "items" => [
                    "player_number" => ["x" => 3.1, "y" => 1.36, "rotate" => 0, "size" => 150]
                ]
            ],
            "Number Back" => [
                "items" => [
                    "player_number" => ["x" => 3.1, "y" => 1.26, "rotate" => 0, "size" => 70]
                ]
            ],                
            "Neck Patch" => [
                "items" => [
                    "l_shoulsadasder_logo_b" => ["x" => 2.2, "y" => 7.1, "rotate" => 90]
                ]
            ],
            "Logo" => [
                "items" => [
                    "teamname" => ["x" => 1.7, "y" => 3.0, "rotate" => 0, "height" => 50, "width" => 50]
                ]
            ],
            "Text Style 1" => [
                "items" => [
                    "teamname" => ["x" => 1.85, "y" => 3.9, "rotate" => 0, "size" => 20]
                ]
            ],
            "Front Number" => [
                "items" => [
                    "front_number" => ["x" => 1.85, "y" => 3.0, "rotate" => 0, "size" => 20]
                ]
            ],
            "Upper Chest" => [
                "items" => [
                    "Upper_chest_flag" => ["x" => 1.85, "y" => 3.5, "rotate" => 0, "height" => 30, "width" => 20]
                ]
            ],
            "Upper Chest Logo" => [
                "items" => [
                    "Upper_chest_flag" => ["x" => 1.85, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
            "Crest" => [
                "items" => [
                    "front_crest_logo" => ["x" => 1.45, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
            "Crest Logo" => [
                "items" => [
                    "front_crest_logo" => ["x" => 1.45, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
            "Shoulder Logo Left" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslf-3" => ["x" => 2.55, "y" => 5.4, "rotate" => 90 , "height" => 30, "width" => 30],
                    "lslf-2" => ["x" => 3.2, "y" => 2.05, "rotate" => 270, "height" => 30, "width" => 30] // 👈 front sleeve
                ]
            ],
            "Shoulder Logo Right" => [
                "items" => [
                    "lslf" => ["x" => 3, "y" => 1.48, "rotate" =>  90, "height" => 30, "width" => 30],
                    "lslf-4" => ["x" => 1.45, "y" => 400, "rotate" => 270, "height" => 30, "width" => 30] // 👈 front sleeve
                ]
            ],
            // 👉 add default map here
        ],
        117 => [
            "Left  Sleeve Number" => [
                "items" => [
                    "b_rightnumber" => ["x" => 6.2, "y" => 1.81, "rotate" => 90,"size" => 190],
                    "f_leftnumber" => ["x" => 2.8, "y" => 10, "rotate" => 90, "size" => 190]
                ]
            ],
            "Sleeve Number Left" => [
                "items" => [
                    "b_rightnumber" => ["x" => 5.5, "y" => 1.69, "rotate" => 90,"size" => 30],
                    "f_leftnumber" => ["x" => 2.5, "y" => 9, "rotate" => 90, "size" => 30]
                ]
            ],
            "Right Sleeve Number" => [
                "items" => [
                    "b_leftnumber" => ["x" => 2.05, "y" => 1.81, "rotate" =>  270,"size" => 190],
                    "f_rightnumber" => ["x" => 1.46, "y" => 10, "rotate" => 270,"size" => 190] // 👈 front sleeve
                ]
            ],
            "Sleeve Number Right" => [
                "items" => [
                    "b_leftnumber" => ["x" => 2, "y" => 1.68, "rotate" =>  270,"size" => 30],
                    "f_rightnumber" => ["x" => 1.40, "y" => 9, "rotate" => 270,"size" =>30] // 👈 front sleeve
                ]
            ],
            "Front Image" => [
                "items" => [
                    "front_logo" => ["x" => 2.03, "y" => 4.6, "rotate" => 0]
                ]
            ],
            "Front" => [
                "items" => [
                    "front_logo" => ["x" => 1.75, "y" => 3.4, "rotate" => 0, "height" => 80, "width" => 80]
                ]
            ],    
            "Front Logo" => [
                "items" => [
                    "front_logo" => ["x" => 1.75, "y" => 3.4, "rotate" => 0, "height" => 80, "width" => 80]
                ]
            ],    
            "Back Name" => [
                "items" => [
                    "player_name" => ["x" => 3.1, "y" => 1.58, "rotate" => 0, "size" => 160]
                ]
            ],
            "Name Back" => [
                "items" => [
                    "player_name" => ["x" => 2.95, "y" => 1.48, "rotate" => 0, "size" => 25]
                ]
            ],
            "Back Number" => [
                "items" => [
                    "player_number" => ["x" => 3.1, "y" => 1.36, "rotate" => 0, "size" => 150]
                ]
            ],
            "Number Back" => [
                "items" => [
                    "player_number" => ["x" => 2.95, "y" => 1.26, "rotate" => 0, "size" => 70]
                ]
            ],
            "Right Shoulder" => [
                "items" => [
                    "lslf" => ["x" => 3.68, "y" => 3.3, "rotate" =>  270],
                    "lslf-2" => ["x" => 1.7, "y" => 56, "rotate" => 270] // 👈 front sleeve
                ]
            ],
            "Shoulder  Right" => [
                "items" => [
                    "lslf" => ["x" => 3.68, "y" => 3.3, "rotate" =>  270, "height" => 30, "width" => 30],
                    "lslf-4" => ["x" => 1.47, "y" => 400, "rotate" => 270, "height" => 30, "width" => 30] // 👈 front sleeve
                ]
            ],    
            "Left Shoulder" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslb" => ["x" => 5.35, "y" => 4.7, "rotate" => 90]
                ]
            ],
            "Shoulder  Left" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslf-3" => ["x" => 2.49, "y" => 5.4, "rotate" => 90 , "height" => 30, "width" => 30]
                ]
            ],    
            "Neck Patch" => [
                "items" => [
                    "l_shoulsadasder_logo_b" => ["x" => 2.2, "y" => 7.1, "rotate" => 90]
                ]
            ],
            "Logo" => [
                "items" => [
                    "teamname" => ["x" => 1.7, "y" => 3.0, "rotate" => 0, "height" => 50, "width" => 50]
                ]
            ],
            "Text Style 1" => [
                "items" => [
                    "teamname" => ["x" => 1.85, "y" => 3.9, "rotate" => 0, "size" => 20]
                ]
            ],
            "Front Number" => [
                "items" => [
                    "front_number" => ["x" => 1.85, "y" => 3.0, "rotate" => 0, "size" => 20]
                ]
            ],
            "Upper Chest" => [
                "items" => [
                    "Upper_chest_flag" => ["x" => 1.9, "y" => 3.4, "rotate" => 0, "height" => 30, "width" => 30]
                ]
            ],
            "Upper Chest Logo" => [
                "items" => [
                    "Upper_chest_flag" => ["x" => 1.85, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
            "Crest Logo" => [
                "items" => [
                    "front_crest_logo" => ["x" => 1.45, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
            "Chest Logo" => [
                "items" => [
                    "front_crest_logo" => ["x" => 1.45, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
            "Shoulder Logo Left" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslf-3" => ["x" => 2.45, "y" => 5.4, "rotate" => 90 , "height" => 30, "width" => 30],
                    "lslf-2" => ["x" => 3.2, "y" => 2.1, "rotate" => 270, "height" => 30, "width" => 30] 
                ]
            ],
            "Shoulder Logo Right" => [
                "items" => [
                    "lslf" => ["x" => 3.68, "y" => 3.3, "rotate" =>  270, "height" => 30, "width" => 30],
                    "lslf-4" => ["x" => 1.44, "y" => 400, "rotate" => 270, "height" => 30, "width" => 30], // 👈 front sleeve
                    "lslf" => ["x" => 2.8, "y" => 1.52, "rotate" =>  90, "height" => 30, "width" => 30]
                ]
            ],
            // 👉 add default map here
        ],
        113 => [
            "Left  Sleeve Number" => [
                "items" => [
                    "b_rightnumber" => ["x" => 6.2, "y" => 1.81, "rotate" => 90,"size" => 190],
                    "f_leftnumber" => ["x" => 2.8, "y" => 10, "rotate" => 90, "size" => 190]
                ]
            ],
            "Shoulder Left" => [
                "items" => [
                    "b_rightnumber" => ["x" => 5.5, "y" => 1.68, "rotate" => 90,"size" => 30],
                    "f_leftnumber" => ["x" => 2.5, "y" => 9, "rotate" => 90, "size" => 30]
                ]
            ],
            "Right Sleeve Number" => [
                "items" => [
                    "b_leftnumber" => ["x" => 2.05, "y" => 1.81, "rotate" =>  270,"size" => 190],
                    "f_rightnumber" => ["x" => 1.46, "y" => 10, "rotate" => 270,"size" => 190] // 👈 front sleeve
                ]
            ],
            "Shoulder Right" => [
                "items" => [
                    "b_leftnumber" => ["x" => 2.08, "y" => 1.68, "rotate" =>  270,"size" => 30],
                    "f_rightnumber" => ["x" => 1.46, "y" => 9, "rotate" => 270,"size" =>30] // 👈 front sleeve
                ]
            ],
            "Front Image" => [
                "items" => [
                    "front_logo" => ["x" => 2.03, "y" => 4.6, "rotate" => 0]
                ]
            ],
            "Front" => [
                "items" => [
                    "front_logo" => ["x" => 1.75, "y" => 3.4, "rotate" => 0, "height" => 80, "width" => 80]
                ]
            ],    
            "Front Logo" => [
                "items" => [
                    "front_logo" => ["x" => 1.75, "y" => 3.4, "rotate" => 0, "height" => 80, "width" => 80]
                ]
            ],    
            "Back Name" => [
                "items" => [
                    "player_name" => ["x" => 3.1, "y" => 1.58, "rotate" => 0, "size" => 160]
                ]
            ],
            "Name Back" => [
                "items" => [
                    "player_name" => ["x" => 3.1, "y" => 1.48, "rotate" => 0, "size" => 20]
                ]
            ],
            "Back Number" => [
                "items" => [
                    "player_number" => ["x" => 3.1, "y" => 1.36, "rotate" => 0, "size" => 150]
                ]
            ],
            "Number Back" => [
                "items" => [
                    "player_number" => ["x" => 3.1, "y" => 1.26, "rotate" => 0, "size" => 70]
                ]
            ],
            "Right Shoulder" => [
                "items" => [
                    "lslf" => ["x" => 3.68, "y" => 3.3, "rotate" =>  270],
                    "lslf-2" => ["x" => 1.7, "y" => 56, "rotate" => 270] // 👈 front sleeve
                ]
            ],
            "Shoulder  Right" => [
                "items" => [
                    "lslf" => ["x" => 3.68, "y" => 3.3, "rotate" =>  270, "height" => 30, "width" => 30],
                    "lslf-4" => ["x" => 1.47, "y" => 400, "rotate" => 270, "height" => 30, "width" => 30] // 👈 front sleeve
                ]
            ],    
            "Left Shoulder" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslb" => ["x" => 5.35, "y" => 4.7, "rotate" => 90]
                ]
            ],
            "Shoulder  Left" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslf-3" => ["x" => 2.49, "y" => 5.4, "rotate" => 90 , "height" => 30, "width" => 30]
                ]
            ],    
            "Neck Patch" => [
                "items" => [
                    "l_shoulsadasder_logo_b" => ["x" => 2.2, "y" => 7.1, "rotate" => 90]
                ]
            ],
            "Logo" => [
                "items" => [
                    "teamname" => ["x" => 1.7, "y" => 3.0, "rotate" => 0, "height" => 50, "width" => 50]
                ]
            ],
            "Text Style 1" => [
                "items" => [
                    "teamname" => ["x" => 1.85, "y" => 3.9, "rotate" => 0, "size" => 20]
                ]
            ],
            "Front Number" => [
                "items" => [
                    "front_number" => ["x" => 1.85, "y" => 3.0, "rotate" => 0, "size" => 20]
                ]
            ],
            "Upper Chest" => [
                "items" => [
                    "Upper_chest_flag" => ["x" => 1.9, "y" => 3.4, "rotate" => 0, "height" => 30, "width" => 30]
                ]
            ],
            "Upper Chest Logo" => [
                "items" => [
                    "Upper_chest_flag" => ["x" => 1.85, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
            "Crest Logo" => [
                "items" => [
                    "front_crest_logo" => ["x" => 1.45, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
            "Crest" => [
                "items" => [
                    "front_crest_logo" => ["x" => 1.45, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
            "Shoulder Left" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslf-3" => ["x" => 2.45, "y" => 5.4, "rotate" => 90 , "height" => 30, "width" => 30]
                ]
            ],
            "Shoulder Right" => [
                "items" => [
                    "lslf" => ["x" => 3.68, "y" => 3.3, "rotate" =>  270, "height" => 30, "width" => 30],
                    "lslf-4" => ["x" => 1.44, "y" => 400, "rotate" => 270, "height" => 30, "width" => 30] // 👈 front sleeve
                ]
            ],
            // 👉 add default map here
        ],
        80 => [            
            "Sleeve Number Left" => [
                "items" => [
                    "b_rightnumber" => ["x" => 6.4, "y" => 1.66, "rotate" => 90,"size" => 30],
                    "f_leftnumber" => ["x" => 2.5, "y" => 9, "rotate" => 90, "size" => 30]
                ]
            ],            
            "Sleeve Number Right" => [
                "items" => [
                    "b_leftnumber" => ["x" => 2.1, "y" => 1.66, "rotate" =>  270,"size" => 30],
                    "f_rightnumber" => ["x" => 1.46, "y" => 9, "rotate" => 270,"size" =>30] // 👈 front sleeve
                ]
            ],
            "Front Image" => [
                "items" => [
                    "front_logo" => ["x" => 2.03, "y" => 4.6, "rotate" => 0]
                ]
            ],
            "Front" => [
                "items" => [
                    "front_logo" => ["x" => 1.75, "y" => 3.4, "rotate" => 0, "height" => 80, "width" => 80]
                ]
            ],    
            "Front Logo" => [
                "items" => [
                    "front_logo" => ["x" => 1.75, "y" => 3.4, "rotate" => 0, "height" => 80, "width" => 80]
                ]
            ],    
            "Back Name" => [
                "items" => [
                    "player_name" => ["x" => 3.1, "y" => 1.58, "rotate" => 0, "size" => 160]
                ]
            ],
            "Name Back" => [
                "items" => [
                    "player_name" => ["x" => 3.1, "y" => 1.48, "rotate" => 0, "size" => 20]
                ]
            ],
            "Back Number" => [
                "items" => [
                    "player_number" => ["x" => 3.1, "y" => 1.36, "rotate" => 0, "size" => 150]
                ]
            ],
            "Number Back" => [
                "items" => [
                    "player_number" => ["x" => 3.1, "y" => 1.26, "rotate" => 0, "size" => 70]
                ]
            ],
            "Right Shoulder" => [
                "items" => [
                    "lslf" => ["x" => 3.68, "y" => 3.3, "rotate" =>  270],
                    "lslf-2" => ["x" => 1.7, "y" => 56, "rotate" => 270] // 👈 front sleeve
                ]
            ],
            "Shoulder  Right" => [
                "items" => [
                    "lslf" => ["x" => 3.68, "y" => 3.3, "rotate" =>  270, "height" => 30, "width" => 30],
                    "lslf-4" => ["x" => 1.47, "y" => 400, "rotate" => 270, "height" => 30, "width" => 30] // 👈 front sleeve
                ]
            ],    
            "Left Shoulder" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslb" => ["x" => 5.35, "y" => 4.7, "rotate" => 90]
                ]
            ],
            "Shoulder  Left" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslf-3" => ["x" => 2.49, "y" => 5.4, "rotate" => 90 , "height" => 30, "width" => 30]
                ]
            ],    
            "Neck Patch" => [
                "items" => [
                    "l_shoulsadasder_logo_b" => ["x" => 2.2, "y" => 7.1, "rotate" => 90]
                ]
            ],
            "Logo" => [
                "items" => [
                    "teamname" => ["x" => 1.7, "y" => 3.0, "rotate" => 0, "height" => 50, "width" => 50]
                ]
            ],
            "Text Style 1" => [
                "items" => [
                    "teamname" => ["x" => 1.85, "y" => 3.9, "rotate" => 0, "size" => 20]
                ]
            ],
            "Front Number" => [
                "items" => [
                    "front_number" => ["x" => 1.85, "y" => 3.0, "rotate" => 0, "size" => 20]
                ]
            ],
            "Upper Chest" => [
                "items" => [
                    "Upper_chest_flag" => ["x" => 1.9, "y" => 3.4, "rotate" => 0, "height" => 30, "width" => 30]
                ]
            ],
            "Upper Chest Logo" => [
                "items" => [
                    "Upper_chest_flag" => ["x" => 1.85, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
            "Crest Logo" => [
                "items" => [
                    "front_crest_logo" => ["x" => 1.45, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
            "Shoulder Logo Left" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslf-3" => ["x" => 2.45, "y" => 5.4, "rotate" => 90 , "height" => 30, "width" => 30]
                ]
            ],
            "Shoulder Logo Right" => [
                "items" => [
                    "lslf" => ["x" => 3.68, "y" => 3.3, "rotate" =>  270, "height" => 30, "width" => 30],
                    "lslf-4" => ["x" => 1.51, "y" => 400, "rotate" => 270, "height" => 30, "width" => 30] // 👈 front sleeve
                ]
            ],
            // 👉 add default map here
        ],
        75 => [
          
            "Sleeve Number Left" => [
                "items" => [
                    "b_rightnumber" => ["x" => 6.2, "y" => 1.66, "rotate" => 90,"size" => 30],
                    "f_leftnumber" => ["x" => 2.67, "y" => 9, "rotate" => 90, "size" => 30]
                ]
            ],
            "Sleeve Number Right" => [
                "items" => [
                    "b_leftnumber" => ["x" => 2.08, "y" => 1.66, "rotate" =>  270,"size" => 30],
                    "f_rightnumber" => ["x" => 1.42, "y" => 9.5, "rotate" => 270,"size" =>30] // 👈 front sleeve
                ]
            ],
            "Front Image" => [
                "items" => [
                    "front_logo" => ["x" => 2.03, "y" => 4.6, "rotate" => 0]
                ]
            ],
            "Front" => [
                "items" => [
                    "front_logo" => ["x" => 1.75, "y" => 3.4, "rotate" => 0, "height" => 80, "width" => 80]
                ]
            ],    
            "Front Logo" => [
                "items" => [
                    "front_logo" => ["x" => 1.75, "y" => 3.4, "rotate" => 0, "height" => 80, "width" => 80]
                ]
            ],    
            "Back Name" => [
                "items" => [
                    "player_name" => ["x" => 3.1, "y" => 1.58, "rotate" => 0, "size" => 160]
                ]
            ],
            "Name Back" => [
                "items" => [
                    "player_name" => ["x" => 3.1, "y" => 1.48, "rotate" => 0, "size" => 20]
                ]
            ],
            "Back Number" => [
                "items" => [
                    "player_number" => ["x" => 3.1, "y" => 1.36, "rotate" => 0, "size" => 150]
                ]
            ],
            "Number Back" => [
                "items" => [
                    "player_number" => ["x" => 3.1, "y" => 1.26, "rotate" => 0, "size" => 70]
                ]
            ],
            "Right Shoulder" => [
                "items" => [
                    "lslf" => ["x" => 3.68, "y" => 3.3, "rotate" =>  270],
                    "lslf-2" => ["x" => 1.7, "y" => 56, "rotate" => 270] // 👈 front sleeve
                ]
            ],
            "Shoulder  Right" => [
                "items" => [
                    "lslf" => ["x" => 3.68, "y" => 3.3, "rotate" =>  270, "height" => 30, "width" => 30],
                    "lslf-4" => ["x" => 1.47, "y" => 400, "rotate" => 270, "height" => 30, "width" => 30] // 👈 front sleeve
                ]
            ],    
            "Left Shoulder" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslb" => ["x" => 5.35, "y" => 4.7, "rotate" => 90]
                ]
            ],
            "Shoulder  Left" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslf-3" => ["x" => 2.49, "y" => 5.4, "rotate" => 90 , "height" => 30, "width" => 30]
                ]
            ],    
            "Neck Patch" => [
                "items" => [
                    "l_shoulsadasder_logo_b" => ["x" => 2.2, "y" => 7.1, "rotate" => 90]
                ]
            ],
            "Logo" => [
                "items" => [
                    "teamname" => ["x" => 1.7, "y" => 3.0, "rotate" => 0, "height" => 50, "width" => 50]
                ]
            ],
            "Text Style 1" => [
                "items" => [
                    "teamname" => ["x" => 1.85, "y" => 3.9, "rotate" => 0, "size" => 20]
                ]
            ],
            "Front Number" => [
                "items" => [
                    "front_number" => ["x" => 1.85, "y" => 3.0, "rotate" => 0, "size" => 20]
                ]
            ],
            "Number Front" => [
                "items" => [
                    "front_number" => ["x" => 1.85, "y" => 3.0, "rotate" => 0, "size" => 20]
                ]
            ],
            "Upper Chest" => [
                "items" => [
                    "Upper_chest_flag" => ["x" => 1.9, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
            "Upper Chest Logo" => [
                "items" => [
                    "Upper_chest_flag" => ["x" => 1.85, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
             "Crest Logo" => [
                "items" => [
                    "front_crest_logo" => ["x" => 1.45, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],     
            "Shoulder Logo Left" => [
                "items" => [
                    "rslb" => ["x" => 34, "y" => 1.58, "rotate" => 90],
                    "lslf-3" => ["x" => 2.51, "y" => 5.4, "rotate" => 90 , "height" => 30, "width" => 30]
                ]
            ],
            "Shoulder Logo Right" => [
                "items" => [
                    "lslf" => ["x" => 3.68, "y" => 3.3, "rotate" =>  270, "height" => 30, "width" => 30],
                    "lslf-4" => ["x" => 1.47, "y" => 400, "rotate" => 270, "height" => 30, "width" => 30] // 👈 front sleeve
                ]
            ],
            // 👉 add default map here
        ],
        103 => [
            "Left  Sleeve Number" => [
                "items" => [
                    "b_rightnumber" => ["x" => 6.2, "y" => 1.81, "rotate" => 90,"size" => 190],
                    "f_leftnumber" => ["x" => 2.8, "y" => 10, "rotate" => 90, "size" => 190]
                ]
            ],
            "Sleeve Number Left" => [
                "items" => [
                    "b_rightnumber" => ["x" => 6.2, "y" => 1.66, "rotate" => 90,"size" => 30],
                    "f_leftnumber" => ["x" => 2.67, "y" => 9, "rotate" => 90, "size" => 30]
                ]
            ],
            "Right Sleeve Number" => [
                "items" => [
                    "b_leftnumber" => ["x" => 2.05, "y" => 1.81, "rotate" =>  270,"size" => 190],
                    "f_rightnumber" => ["x" => 1.46, "y" => 10, "rotate" => 270,"size" => 190] // 👈 front sleeve
                ]
            ],
            "Sleeve Number Right" => [
                "items" => [
                    "b_leftnumber" => ["x" => 2.08, "y" => 1.66, "rotate" =>  270,"size" => 30],
                    "f_rightnumber" => ["x" => 1.42, "y" => 9.5, "rotate" => 270,"size" =>30] // 👈 front sleeve
                ]
            ],
            "Front Image" => [
                "items" => [
                    "front_logo" => ["x" => 2.03, "y" => 4.6, "rotate" => 0]
                ]
            ],
            "Front" => [
                "items" => [
                    "front_logo" => ["x" => 1.75, "y" => 3.4, "rotate" => 0, "height" => 80, "width" => 80]
                ]
            ],    
            "Front Logo" => [
                "items" => [
                    "front_logo" => ["x" => 1.75, "y" => 3.4, "rotate" => 0, "height" => 80, "width" => 80]
                ]
            ],    
            "Back Name" => [
                "items" => [
                    "player_name" => ["x" => 3.1, "y" => 1.58, "rotate" => 0, "size" => 160]
                ]
            ],
            "Name Back" => [
                "items" => [
                    "player_name" => ["x" => 3.1, "y" => 1.48, "rotate" => 0, "size" => 20]
                ]
            ],
            "Back Number" => [
                "items" => [
                    "player_number" => ["x" => 3.1, "y" => 1.36, "rotate" => 0, "size" => 150]
                ]
            ],
            "Number Back" => [
                "items" => [
                    "player_number" => ["x" => 3.1, "y" => 1.26, "rotate" => 0, "size" => 70]
                ]
            ],
            "Right Shoulder" => [
                "items" => [
                    "lslf" => ["x" => 3.68, "y" => 3.3, "rotate" =>  270],
                    "lslf-2" => ["x" => 1.7, "y" => 56, "rotate" => 270] // 👈 front sleeve
                ]
            ],
            "Shoulder  Right" => [
                "items" => [
                    "lslf" => ["x" => 3.68, "y" => 3.3, "rotate" =>  270, "height" => 30, "width" => 30],
                    "lslf-4" => ["x" => 1.47, "y" => 400, "rotate" => 270, "height" => 30, "width" => 30] // 👈 front sleeve
                ]
            ],    
            "Left Shoulder" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslb" => ["x" => 5.35, "y" => 4.7, "rotate" => 90]
                ]
            ],
            "Shoulder  Left" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslf-3" => ["x" => 2.49, "y" => 5.4, "rotate" => 90 , "height" => 30, "width" => 30]
                ]
            ],    
            "Neck Patch" => [
                "items" => [
                    "l_shoulsadasder_logo_b" => ["x" => 2.2, "y" => 7.1, "rotate" => 90]
                ]
            ],
            "Logo" => [
                "items" => [
                    "teamname" => ["x" => 1.7, "y" => 3.0, "rotate" => 0, "height" => 50, "width" => 50]
                ]
            ],
            "Text Style 1" => [
                "items" => [
                    "teamname" => ["x" => 1.85, "y" => 3.9, "rotate" => 0, "size" => 20]
                ]
            ],
            "Front Number" => [
                "items" => [
                    "front_number" => ["x" => 1.85, "y" => 3.0, "rotate" => 0, "size" => 20]
                ]
            ],
            "Number Front" => [
                "items" => [
                    "front_number" => ["x" => 1.85, "y" => 3.0, "rotate" => 0, "size" => 20]
                ]
            ],
            "Upper Chest" => [
                "items" => [
                    "Upper_chest_flag" => ["x" => 1.9, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
            "Upper Chest Logo" => [
                "items" => [
                    "Upper_chest_flag" => ["x" => 1.85, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
             "Crest Logo" => [
                "items" => [
                    "front_crest_logo" => ["x" => 1.45, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],     
            "Shoulder Logo Left" => [
                "items" => [
                    "rslb" => ["x" => 34, "y" => 1.58, "rotate" => 90],
                    "lslf-3" => ["x" => 2.51, "y" => 5.4, "rotate" => 90 , "height" => 30, "width" => 30]
                ]
            ],
            "Shoulder Logo Right" => [
                "items" => [
                    "lslf" => ["x" => 3.68, "y" => 3.3, "rotate" =>  270, "height" => 30, "width" => 30],
                    "lslf-4" => ["x" => 1.47, "y" => 400, "rotate" => 270, "height" => 30, "width" => 30] // 👈 front sleeve
                ]
            ],
            // 👉 add default map here
        ], 

        114 => [
            "Left  Sleeve Number" => [
                "items" => [
                    "b_rightnumber" => ["x" => 6.2, "y" => 1.81, "rotate" => 90,"size" => 190],
                    "f_leftnumber" => ["x" => 2.8, "y" => 10, "rotate" => 90, "size" => 190]
                ]
            ],
            "Sleeve Number Left" => [
                "items" => [
                    "b_rightnumber" => ["x" => 5.5, "y" => 1.66, "rotate" => 90,"size" => 30],
                    "f_leftnumber" => ["x" => 2.5, "y" => 9, "rotate" => 90, "size" => 30]
                ]
            ],
            "Right Sleeve Number" => [
                "items" => [
                    "b_leftnumber" => ["x" => 2.05, "y" => 1.81, "rotate" =>  270,"size" => 190],
                    "f_rightnumber" => ["x" => 1.46, "y" => 10, "rotate" => 270,"size" => 190] // 👈 front sleeve
                ]
            ],
            "Sleeve Number Right" => [
                "items" => [
                    "b_leftnumber" => ["x" => 2.18, "y" => 1.66, "rotate" =>  270,"size" => 30],
                    "f_rightnumber" => ["x" => 1.46, "y" => 9, "rotate" => 270,"size" =>30] // 👈 front sleeve
                ]
            ],
            "Front Image" => [
                "items" => [
                    "front_logo" => ["x" => 2.03, "y" => 4.6, "rotate" => 0]
                ]
            ],
            "Front" => [
                "items" => [
                    "front_logo" => ["x" => 1.75, "y" => 3.4, "rotate" => 0, "height" => 80, "width" => 80]
                ]
            ],    
            "Front Logo" => [
                "items" => [
                    "front_logo" => ["x" => 1.75, "y" => 3.4, "rotate" => 0, "height" => 80, "width" => 80]
                ]
            ],    
            "Back Name" => [
                "items" => [
                    "player_name" => ["x" => 3.1, "y" => 1.58, "rotate" => 0, "size" => 160]
                ]
            ],
            "Name Back" => [
                "items" => [
                    "player_name" => ["x" => 3.1, "y" => 1.48, "rotate" => 0, "size" => 20]
                ]
            ],
            "Back Number" => [
                "items" => [
                    "player_number" => ["x" => 3.1, "y" => 1.36, "rotate" => 0, "size" => 150]
                ]
            ],
            "Number Back" => [
                "items" => [
                    "player_number" => ["x" => 3.1, "y" => 1.26, "rotate" => 0, "size" => 70]
                ]
            ],
            "Right Shoulder" => [
                "items" => [
                    "lslf" => ["x" => 3.68, "y" => 3.3, "rotate" =>  270],
                    "lslf-2" => ["x" => 1.7, "y" => 56, "rotate" => 270] // 👈 front sleeve
                ]
            ],
            "Shoulder  Right" => [
                "items" => [
                    "lslf" => ["x" => 3.68, "y" => 3.3, "rotate" =>  270, "height" => 30, "width" => 30],
                    "lslf-4" => ["x" => 1.47, "y" => 400, "rotate" => 270, "height" => 30, "width" => 30] // 👈 front sleeve
                ]
            ],    
            "Left Shoulder" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslb" => ["x" => 5.35, "y" => 4.7, "rotate" => 90]
                ]
            ],
            "Shoulder  Left" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslf-3" => ["x" => 2.47, "y" => 5.4, "rotate" => 90 , "height" => 30, "width" => 30]
                ]
            ],    
            "Neck Patch" => [
                "items" => [
                    "l_shoulsadasder_logo_b" => ["x" => 2.2, "y" => 7.1, "rotate" => 90]
                ]
            ],
            "Logo" => [
                "items" => [
                    "teamname" => ["x" => 1.7, "y" => 3.0, "rotate" => 0, "height" => 50, "width" => 50]
                ]
            ],
            "Text Style 1" => [
                "items" => [
                    "teamname" => ["x" => 1.85, "y" => 3.9, "rotate" => 0, "size" => 20]
                ]
            ],
            "Number Front" => [
                "items" => [
                    "front_number" => ["x" => 1.85, "y" => 3.0, "rotate" => 0, "size" => 20]
                ]
            ],
            "Upper Chest" => [
                "items" => [
                    "Upper_chest_flag" => ["x" => 1.9, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
            "Upper Chest Logo" => [
                "items" => [
                    "Upper_chest_flag" => ["x" => 1.85, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
            "Crest" => [
                "items" => [
                    "front_crest_logo" => ["x" => 1.45, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
            "Shoulder Logo Left" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslf-3" => ["x" => 2.47, "y" => 5.4, "rotate" => 90 , "height" => 30, "width" => 30]
                ]
            ],
            "Shoulder Logo Right" => [
                "items" => [
                    "lslf" => ["x" => 3.68, "y" => 3.3, "rotate" =>  270, "height" => 30, "width" => 30],
                    "lslf-4" => ["x" => 1.48, "y" => 400, "rotate" => 270, "height" => 30, "width" => 30] // 👈 front sleeve
                ]
            ],
            // 👉 add default map here
        ],
        

        95 => [
            "Left  Sleeve Number" => [
                "items" => [
                    "b_rightnumber" => ["x" => 6.2, "y" => 1.81, "rotate" => 90,"size" => 190],
                    "f_leftnumber" => ["x" => 2.8, "y" => 10, "rotate" => 90, "size" => 190]
                ]
            ],
            "Sleeve Number Left" => [
                "items" => [
                    "b_rightnumber" => ["x" => 5.5, "y" => 1.66, "rotate" => 90,"size" => 30],
                    "f_leftnumber" => ["x" => 2.5, "y" => 9, "rotate" => 90, "size" => 30]
                ]
            ],
            "Right Sleeve Number" => [
                "items" => [
                    "b_leftnumber" => ["x" => 2.05, "y" => 1.81, "rotate" =>  270,"size" => 190],
                    "f_rightnumber" => ["x" => 1.46, "y" => 10, "rotate" => 270,"size" => 190] // 👈 front sleeve
                ]
            ],
            "Sleeve Number Right" => [
                "items" => [
                    "b_leftnumber" => ["x" => 2.18, "y" => 1.66, "rotate" =>  270,"size" => 30],
                    "f_rightnumber" => ["x" => 1.46, "y" => 9, "rotate" => 270,"size" =>30] // 👈 front sleeve
                ]
            ],
            "Front Image" => [
                "items" => [
                    "front_logo" => ["x" => 2.03, "y" => 4.6, "rotate" => 0]
                ]
            ],
            "Front" => [
                "items" => [
                    "front_logo" => ["x" => 1.75, "y" => 3.4, "rotate" => 0, "height" => 80, "width" => 80]
                ]
            ],    
            "Front Logo" => [
                "items" => [
                    "front_logo" => ["x" => 1.75, "y" => 3.4, "rotate" => 0, "height" => 80, "width" => 80]
                ]
            ],    
            "Back Name" => [
                "items" => [
                    "player_name" => ["x" => 3.1, "y" => 1.58, "rotate" => 0, "size" => 160]
                ]
            ],
            "Name Back" => [
                "items" => [
                    "player_name" => ["x" => 3.1, "y" => 1.48, "rotate" => 0, "size" => 20]
                ]
            ],
            "Back Number" => [
                "items" => [
                    "player_number" => ["x" => 3.1, "y" => 1.36, "rotate" => 0, "size" => 150]
                ]
            ],
            "Number Back" => [
                "items" => [
                    "player_number" => ["x" => 3.1, "y" => 1.26, "rotate" => 0, "size" => 70]
                ]
            ],
            "Right Shoulder" => [
                "items" => [
                    "lslf" => ["x" => 3.68, "y" => 3.3, "rotate" =>  270],
                    "lslf-2" => ["x" => 1.7, "y" => 56, "rotate" => 270] // 👈 front sleeve
                ]
            ],
            "Shoulder  Right" => [
                "items" => [
                    "lslf" => ["x" => 3.68, "y" => 3.3, "rotate" =>  270, "height" => 30, "width" => 30],
                    "lslf-4" => ["x" => 1.47, "y" => 400, "rotate" => 270, "height" => 30, "width" => 30] // 👈 front sleeve
                ]
            ],    
            "Left Shoulder" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslb" => ["x" => 5.35, "y" => 4.7, "rotate" => 90]
                ]
            ],
            "Shoulder  Left" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslf-3" => ["x" => 2.47, "y" => 5.4, "rotate" => 90 , "height" => 30, "width" => 30]
                ]
            ],    
            "Neck Patch" => [
                "items" => [
                    "l_shoulsadasder_logo_b" => ["x" => 2.2, "y" => 7.1, "rotate" => 90]
                ]
            ],
            "Logo" => [
                "items" => [
                    "teamname" => ["x" => 1.7, "y" => 3.0, "rotate" => 0, "height" => 50, "width" => 50]
                ]
            ],
            "Text Style 1" => [
                "items" => [
                    "teamname" => ["x" => 1.85, "y" => 3.9, "rotate" => 0, "size" => 20]
                ]
            ],
            "Number Front" => [
                "items" => [
                    "front_number" => ["x" => 1.85, "y" => 3.0, "rotate" => 0, "size" => 20]
                ]
            ],
            "Upper Chest" => [
                "items" => [
                    "Upper_chest_flag" => ["x" => 1.9, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
            "Upper Chest Logo" => [
                "items" => [
                    "Upper_chest_flag" => ["x" => 1.85, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
            "Crest Logo" => [
                "items" => [
                    "front_crest_logo" => ["x" => 1.45, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
            "Shoulder Logo Left" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslf-3" => ["x" => 2.47, "y" => 5.4, "rotate" => 90 , "height" => 30, "width" => 30]
                ]
            ],
            "Shoulder Logo Right" => [
                "items" => [
                    "lslf" => ["x" => 3.68, "y" => 3.3, "rotate" =>  270, "height" => 30, "width" => 30],
                    "lslf-4" => ["x" => 1.48, "y" => 400, "rotate" => 270, "height" => 30, "width" => 30] // 👈 front sleeve
                ]
            ],
            // 👉 add default map here
        ],


        94 => [
            "Left  Sleeve Number" => [
                "items" => [
                    "b_rightnumber" => ["x" => 6.2, "y" => 1.81, "rotate" => 90,"size" => 190],
                    "f_leftnumber" => ["x" => 2.8, "y" => 10, "rotate" => 90, "size" => 190]
                ]
            ],
            "Sleeve Number Left" => [
                "items" => [
                    "b_rightnumber" => ["x" => 5.5, "y" => 1.66, "rotate" => 90,"size" => 30],
                    "f_leftnumber" => ["x" => 2.5, "y" => 9, "rotate" => 90, "size" => 30]
                ]
            ],
            "Right Sleeve Number" => [
                "items" => [
                    "b_leftnumber" => ["x" => 2.05, "y" => 1.81, "rotate" =>  270,"size" => 190],
                    "f_rightnumber" => ["x" => 1.46, "y" => 10, "rotate" => 270,"size" => 190] // 👈 front sleeve
                ]
            ],
            "Sleeve Number Right" => [
                "items" => [
                    "b_leftnumber" => ["x" => 2.18, "y" => 1.66, "rotate" =>  270,"size" => 30],
                    "f_rightnumber" => ["x" => 1.46, "y" => 9, "rotate" => 270,"size" =>30] // 👈 front sleeve
                ]
            ],
            "Front Image" => [
                "items" => [
                    "front_logo" => ["x" => 2.03, "y" => 4.6, "rotate" => 0]
                ]
            ],
            "Front" => [
                "items" => [
                    "front_logo" => ["x" => 1.75, "y" => 3.4, "rotate" => 0, "height" => 80, "width" => 80]
                ]
            ],    
            "Front Logo" => [
                "items" => [
                    "front_logo" => ["x" => 1.75, "y" => 3.4, "rotate" => 0, "height" => 80, "width" => 80]
                ]
            ],    
            "Back Name" => [
                "items" => [
                    "player_name" => ["x" => 3.1, "y" => 1.58, "rotate" => 0, "size" => 160]
                ]
            ],
            "Name Back" => [
                "items" => [
                    "player_name" => ["x" => 3.1, "y" => 1.48, "rotate" => 0, "size" => 20]
                ]
            ],
            "Back Number" => [
                "items" => [
                    "player_number" => ["x" => 3.1, "y" => 1.36, "rotate" => 0, "size" => 150]
                ]
            ],
            "Number Back" => [
                "items" => [
                    "player_number" => ["x" => 3.1, "y" => 1.26, "rotate" => 0, "size" => 70]
                ]
            ],
            "Right Shoulder" => [
                "items" => [
                    "lslf" => ["x" => 3.68, "y" => 3.3, "rotate" =>  270],
                    "lslf-2" => ["x" => 1.7, "y" => 56, "rotate" => 270] // 👈 front sleeve
                ]
            ],
            "Shoulder  Right" => [
                "items" => [
                    "lslf" => ["x" => 3.68, "y" => 3.3, "rotate" =>  270, "height" => 30, "width" => 30],
                    "lslf-4" => ["x" => 1.47, "y" => 400, "rotate" => 270, "height" => 30, "width" => 30] // 👈 front sleeve
                ]
            ],    
            "Left Shoulder" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslb" => ["x" => 5.35, "y" => 4.7, "rotate" => 90]
                ]
            ],
            "Shoulder  Left" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslf-3" => ["x" => 2.47, "y" => 5.4, "rotate" => 90 , "height" => 30, "width" => 30]
                ]
            ],    
            "Neck Patch" => [
                "items" => [
                    "l_shoulsadasder_logo_b" => ["x" => 2.2, "y" => 7.1, "rotate" => 90]
                ]
            ],
            "Logo" => [
                "items" => [
                    "teamname" => ["x" => 1.7, "y" => 3.0, "rotate" => 0, "height" => 50, "width" => 50]
                ]
            ],
            "Text Style 1" => [
                "items" => [
                    "teamname" => ["x" => 1.85, "y" => 3.9, "rotate" => 0, "size" => 20]
                ]
            ],
            "Front Number" => [
                "items" => [
                    "front_number" => ["x" => 1.85, "y" => 3.0, "rotate" => 0, "size" => 20]
                ]
            ],
            "Upper Chest" => [
                "items" => [
                    "Upper_chest_flag" => ["x" => 1.9, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
            "Upper Chest Logo" => [
                "items" => [
                    "Upper_chest_flag" => ["x" => 1.85, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
            "Crest Logo" => [
                "items" => [
                    "front_crest_logo" => ["x" => 1.45, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
            "Shoulder Logo Left" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslf-3" => ["x" => 2.47, "y" => 5.4, "rotate" => 90 , "height" => 30, "width" => 30]
                ]
            ],
            "Shoulder Logo Right" => [
                "items" => [
                    "lslf" => ["x" => 3.68, "y" => 3.3, "rotate" =>  270, "height" => 30, "width" => 30],
                    "lslf-4" => ["x" => 1.48, "y" => 400, "rotate" => 270, "height" => 30, "width" => 30] // 👈 front sleeve
                ]
            ],
            // 👉 add default map here
        ],

        
         
        107 => [
            "Sleeve Number Left" => [
                "items" => [
                    "b_rightnumber" => ["x" => 5.5, "y" => 1.66, "rotate" => 90,"size" => 30],
                    "f_leftnumber" => ["x" => 2.5, "y" => 9, "rotate" => 90, "size" => 30]
                ]
            ],
            "Sleeve Number Right" => [
                "items" => [
                    "b_leftnumber" => ["x" => 2.18, "y" => 1.66, "rotate" =>  270,"size" => 30],
                    "f_rightnumber" => ["x" => 1.46, "y" => 9, "rotate" => 270,"size" =>30] // 👈 front sleeve
                ]
            ],
            "Front" => [
                "items" => [
                    "front_logo" => ["x" => 1.75, "y" => 3.4, "rotate" => 0, "height" => 80, "width" => 80]
                ]
            ],       
            "Name Back" => [
                "items" => [
                    "player_name" => ["x" => 3.1, "y" => 1.48, "rotate" => 0, "size" => 20]
                ]
            ],
            "Back" => [
                "items" => [
                    "player_number" => ["x" => 3.1, "y" => 1.21, "rotate" => 0, "size" => 70]
                ]
            ],
            "Right Shoulder" => [
                "items" => [
                    "lslf" => ["x" => 3.68, "y" => 3.3, "rotate" =>  270],
                    "lslf-2" => ["x" => 1.7, "y" => 56, "rotate" => 270] // 👈 front sleeve
                ]
            ],
            "Shoulder  Right" => [
                "items" => [
                    "lslf" => ["x" => 3.68, "y" => 3.3, "rotate" =>  270, "height" => 30, "width" => 30],
                    "lslf-4" => ["x" => 1.47, "y" => 400, "rotate" => 270, "height" => 30, "width" => 30] // 👈 front sleeve
                ]
            ],    
            "Left Shoulder" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslb" => ["x" => 5.35, "y" => 4.7, "rotate" => 90]
                ]
            ],
            "Shoulder  Left" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslf-3" => ["x" => 2.49, "y" => 5.4, "rotate" => 90 , "height" => 30, "width" => 30]
                ]
            ],    
            "Neck Patch" => [
                "items" => [
                    "l_shoulsadasder_logo_b" => ["x" => 2.2, "y" => 7.1, "rotate" => 90]
                ]
            ],
            "Logo" => [
                "items" => [
                    "teamname" => ["x" => 1.7, "y" => 3.0, "rotate" => 0, "height" => 50, "width" => 50]
                ]
            ],
            "Text Style 1" => [
                "items" => [
                    "teamname" => ["x" => 1.85, "y" => 3.9, "rotate" => 0, "size" => 20]
                ]
            ],
            "Front Number" => [
                "items" => [
                    "front_number" => ["x" => 1.85, "y" => 3.0, "rotate" => 0, "size" => 20]
                ]
            ],
            "Upper Chest" => [
                "items" => [
                    "Upper_chest_flag" => ["x" => 1.9, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
            "Crest Logo" => [
                "items" => [
                    "front_crest_logo" => ["x" => 1.45, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
            "Shoulder Logo Left" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslf-3" => ["x" => 2.48, "y" => 5.1, "rotate" => 90 , "height" => 30, "width" => 30]
                ]
            ],
            "Shoulder Logo Right" => [
                "items" => [
                    "lslf" => ["x" => 3.68, "y" => 3.3, "rotate" =>  270, "height" => 30, "width" => 30],
                    "lslf-4" => ["x" => 1.49, "y" => 400, "rotate" => 270, "height" => 30, "width" => 30] // 👈 front sleeve
                ]
            ],
            // 👉 add default map here
        ] ,

      
        "default" => [
            "Left  Sleeve Number" => [
                "items" => [
                    "b_rightnumber" => ["x" => 6.2, "y" => 1.81, "rotate" => 90,"size" => 190],
                    "f_leftnumber" => ["x" => 2.8, "y" => 10, "rotate" => 90, "size" => 190]
                ]
            ],
            "Sleeve Number Left" => [
                "items" => [
                    "b_rightnumber" => ["x" => 5.5, "y" => 1.66, "rotate" => 90,"size" => 30],
                    "f_leftnumber" => ["x" => 2.5, "y" => 9, "rotate" => 90, "size" => 30]
                ]
            ],
            "Right Sleeve Number" => [
                "items" => [
                    "b_leftnumber" => ["x" => 2.05, "y" => 1.81, "rotate" =>  270,"size" => 190],
                    "f_rightnumber" => ["x" => 1.46, "y" => 10, "rotate" => 270,"size" => 190] // 👈 front sleeve
                ]
            ],
            "Sleeve Number Right" => [
                "items" => [
                    "b_leftnumber" => ["x" => 2.18, "y" => 1.66, "rotate" =>  270,"size" => 30],
                    "f_rightnumber" => ["x" => 1.46, "y" => 9, "rotate" => 270,"size" =>30] // 👈 front sleeve
                ]
            ],
            "Front Image" => [
                "items" => [
                    "front_logo" => ["x" => 2.03, "y" => 4.6, "rotate" => 0]
                ]
            ],
            "Front" => [
                "items" => [
                    "front_logo" => ["x" => 1.75, "y" => 3.4, "rotate" => 0, "height" => 80, "width" => 80]
                ]
            ],    
            "Front Logo" => [
                "items" => [
                    "front_logo" => ["x" => 1.75, "y" => 3.4, "rotate" => 0, "height" => 80, "width" => 80]
                ]
            ],    
            "Back Name" => [
                "items" => [
                    "player_name" => ["x" => 3.1, "y" => 1.58, "rotate" => 0, "size" => 160]
                ]
            ],
            "Name Back" => [
                "items" => [
                    "player_name" => ["x" => 3.1, "y" => 1.48, "rotate" => 0, "size" => 20]
                ]
            ],
            "Back Number" => [
                "items" => [
                    "player_number" => ["x" => 3.1, "y" => 1.36, "rotate" => 0, "size" => 150]
                ]
            ],
            "Number Back" => [
                "items" => [
                    "player_number" => ["x" => 3.1, "y" => 1.26, "rotate" => 0, "size" => 70]
                ]
            ],
            "Right Shoulder" => [
                "items" => [
                    "lslf" => ["x" => 3.68, "y" => 3.3, "rotate" =>  270],
                    "lslf-2" => ["x" => 1.7, "y" => 56, "rotate" => 270] // 👈 front sleeve
                ]
            ],
            "Shoulder Right" => [
                "items" => [
                    "lslf" => ["x" => 3.68, "y" => 3.3, "rotate" =>  270, "height" => 30, "width" => 30],
                    "lslf-4" => ["x" => 1.47, "y" => 400, "rotate" => 270, "height" => 30, "width" => 30] // 👈 front sleeve
                ]
            ],    
            "Left Shoulder" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslb" => ["x" => 5.35, "y" => 4.7, "rotate" => 90]
                ]
            ],
            "Shoulder Left" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslf-3" => ["x" => 2.49, "y" => 5.4, "rotate" => 90 , "height" => 30, "width" => 30]
                ]
            ],    
            "Neck" => [
                "items" => [
                    "l_shoulsadasder_logo_b" => ["x" => 2.2, "y" => 7.1, "rotate" => 90]
                ]
            ],
            "Logo" => [
                "items" => [
                    "teamname" => ["x" => 1.7, "y" => 3.0, "rotate" => 0, "height" => 50, "width" => 50]
                ]
            ],
            "Text Style 1" => [
                "items" => [
                    "teamname" => ["x" => 1.85, "y" => 3.9, "rotate" => 0, "size" => 20]
                ]
            ],
            "Front Number" => [
                "items" => [
                    "front_number" => ["x" => 1.85, "y" => 3.0, "rotate" => 0, "size" => 20]
                ]
            ],
            "Upper Chest Logo Right" => [
                "items" => [
                    "Upper_chest_flag" => ["x" => 1.9, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
            "Upper Chest Logo Left" => [
                "items" => [
                    "Upper_chest_flag" => ["x" => 1.85, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
            "Crest" => [
                "items" => [
                    "front_crest_logo" => ["x" => 1.45, "y" => 3.4, "rotate" => 0, "height" => 20, "width" => 20]
                ]
            ],
            "Shoulder Logo Left" => [
                "items" => [
                    "rslb" => ["x" => 3.35, "y" => 1.58, "rotate" => 90],
                    "lslf-3" => ["x" => 2.45, "y" => 5.4, "rotate" => 90 , "height" => 30, "width" => 30]
                ]
            ],
            "Shoulder Logo Right" => [
                "items" => [
                    "lslf" => ["x" => 3.68, "y" => 3.3, "rotate" =>  270, "height" => 30, "width" => 30],
                    "lslf-4" => ["x" => 1.51, "y" => 400, "rotate" => 270, "height" => 30, "width" => 30] // 👈 front sleeve
                ]
            ],
            // 👉 add default map here
        ]
    ];

    return $maps[$designId] ?? $maps['default'];
}