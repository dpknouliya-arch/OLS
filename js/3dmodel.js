// Add these at the top with your other variables

let loadingProgress = 0;

let loadingStartTime = null;

let loadingInterval = null;

const preloaderElement = document.getElementById("preloader");

const progressTextElement = document.getElementById("preloaderProgress");

const timeRemainingElement = document.getElementById("preloaderTimeRemaining");

let fabricTexture = null;

const textureLoader = new THREE.TextureLoader();



document.addEventListener("DOMContentLoaded", function () {

    let currentModelFilename = null; // Track the current model filename

     let currentModelKey = "default";

     let model = null;

     let selectedMesh = null; // Declare  mesh Selected variable
 

    loadDefaultModelUSER();

    const containeruser = document.getElementById("threejs-container-user");

    const contentuser = document.getElementById("threejs-container-user");

    if (!contentuser) {

        console.error("Container element not found!");

        return;

    }

    const scene = new THREE.Scene();

    let backgroundColor = 0xeeeeee; // Default color

    scene.background = new THREE.Color(backgroundColor);   

    document.getElementById("preloader").style.display = "none";

    // Ambient Light (even base illumination)

    const ambientLight = new THREE.AmbientLight(0xFFFFFF, 0.3);

    scene.add(ambientLight);



    // Main Directional Light (always from camera/front view)

    const mainLight = new THREE.DirectionalLight(0xFFFFFF, 0.2);

    scene.add(mainLight);

    scene.add(mainLight.target); // DirectionalLight needs a target   

    const selectedColors = {

        color1: "#FF0000",

        color2: "#FFFF00"

    };    
    
    // user Model load start

    function loadDesignFromDatabaseUSER() {

        const orderId = document.querySelector("input[name='order_id']").value;

        console.log("🔍 Loading design for order:", orderId);



        $.ajax({

            url: 'ajax/get_order_decal.php',

            type: 'POST',

            dataType: 'json',

            data: { orderId: orderId },

            success: function (response) {

                console.log("✅ DB Response:", response);



                let textDecals = [];

                let imageDecals = [];

                let colorDecals = {};



               try {

                    textDecals = typeof response.textdecals === "string"
                        ? JSON.parse(response.textdecals)
                        : (response.textdecals || []);

                    imageDecals = typeof response.imagedecals === "string"
                        ? JSON.parse(response.imagedecals)
                        : (response.imagedecals || []);

                    colorDecals = typeof response.colorDecals === "string"
                        ? JSON.parse(response.colorDecals)
                        : (response.colorDecals || {});

                } catch (e) {

                    console.error("❌ Error parsing JSON:", e);
                    console.log("textdecals:", response.textdecals);
                    console.log("imagedecals:", response.imagedecals);
                    console.log("colorDecals:", response.colorDecals);
                }



                const model = window.mainModelUser;



                // Store saved colors in global

                window.MESH_COLORS = colorDecals || {};



                // Always set mesh material color to white to prevent black tint

                // model.traverse(mesh => {

                //     if (!mesh.isMesh || !mesh.material) return;

                //     mesh.material.color.set("#ffffff");

                //     mesh.material.map = null;

                //     mesh.material.needsUpdate = true;

                // });



                // Restore text decals

                window.textDecals = (textDecals || []).map(d => ({

                    ...d,

                    offset: new THREE.Vector2(d.offset?.x || 0, d.offset?.y || 0),

                    mesh: model.getObjectByName(d.meshName) || null

                }));



                // Restore image decals

                window.imageDecals = (imageDecals || []).map(d => {
                    const image = new Image();

                    const decal = {
                        ...d,
                        offset: new THREE.Vector2(d.offset?.x || 0, d.offset?.y || 0),
                        mesh: model.getObjectByName(d.meshName) || null,
                        image: image
                    };

                    if (d.imageSrc) {
                        image.crossOrigin = "anonymous";

                        image.onload = () => {
                            updateMeshTextureWithAllDecalsUser();
                        };

                        image.onerror = () => {
                            console.error("Failed to load image:", d.imageSrc);
                        };

                        image.src = d.imageSrc+"?ver=1";
                    }

                    return decal;
                });



                setTimeout(() => {

                    updateMeshTextureWithAllDecalsUser();

                    loadArtAprovel();

                }, 500);

            },

            error: function () {

                console.error('❌ Error fetching design data.');

            }

        });

    }

    function loadDefaultModelUSER() {
        // Show preloader when starting to load default model
        document.getElementById("preloader").style.display = "flex";
        const BASE_3D_URL = window.BASE_3D_URL;
        // Get all checked checkboxes inside the form
        const collerId = document.querySelector("input[name='coller_id']").value;
        const styleId = document.querySelector("input[name='style_id']").value;
        const stripesId = document.querySelector("input[name='stripes_id']").value;
        const orderId = document.querySelector("input[name='order_id']").value;
        const checkedValues = collerId;
        const StyleFValues = styleId;
        const StripeValues = stripesId;
        $.ajax({
            url: 'ajax/get_design_data.php',
            type: 'POST',
            dataType: 'json',
            data: {
                collar: checkedValues,
                style: StyleFValues,
                stripes: StripeValues
            },
            success: function (response) {                        
                const defaultModelUrl = window.APP_CONFIG.S3_BUCKET + 'admin/uploads/designs/models/' + response.model;
                const defaultModelType = "halfSleeves";
                const defaultColorMappings = {
                    Plane: "primary",
                    Plane_1: "secondary"
                };
                window.loadedModel = response.model;
                window.COLOR_MAPPING = {};
                // normalize and set mapping

                window.COLOR_MAPPING[response.primary_color.toUpperCase()] = "primary";

                window.COLOR_MAPPING[response.secondary_color.toUpperCase()] = "secondary";

                window.COLOR_MAPPING[response.tertiary_color.toUpperCase()] = "tertiary";
                // optional: map "white" also to tertiary

                if (response.tertiary_color.toUpperCase() === "#FFFFFF") {
                    window.COLOR_MAPPING["white"] = "tertiary";
                }               
                document.querySelectorAll(".designsItems img").forEach(img => {
                    if (img.dataset.modal && img.dataset.modal.includes("Modal2FullSleeves.glb")) {
                        img.closest('.designsItems').classList.add("active");
                    }
                });
                loadModelUSER(defaultModelUrl, defaultColorMappings, defaultModelType);        
            },

            error: function () {

                console.error('Error fetching design data.');

            }

        });

    }

    function loadModelUSER(modelUrl, colorMappings, modelType) {

        const containeruser = document.getElementById("threejs-container-user");

        containeruser.innerHTML = ""; // clear old canvas if reloading

        currentModelFilename = modelUrl.split('/').pop();

        currentModelKey = currentModelFilename;

        const containerW = containeruser.clientWidth || 600;

        const containerH = containeruser.clientHeight || 500;

        // Scene

        const scene = new THREE.Scene();



        // Camera

        const camera = new THREE.PerspectiveCamera(

            35,

            containerW / containerH,

            0.1,

            1000

        );

        camera.position.set(0, 1, 4.5);



        // Renderer

        const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });

        renderer.setSize(containerW, containerH);

        renderer.setPixelRatio(window.devicePixelRatio);

        containeruser.appendChild(renderer.domElement);



        // Lights

        const light = new THREE.HemisphereLight(0xffffff, 0x444444, 1);

        scene.add(light);



        const dirLight = new THREE.DirectionalLight(0xffffff, 0.8);

        dirLight.position.set(5, 10, 7.5);

        scene.add(dirLight);



        // Controls

        const controls = new THREE.OrbitControls(camera, renderer.domElement);

        controls.enableDamping = true;



        // 🔹 Animate loop

        function animate() {

            requestAnimationFrame(animate);

            controls.update();

            renderer.render(scene, camera);

        }



        // Loader

        const loader = new THREE.GLTFLoader();

        loader.load(modelUrl, function (gltf) {

            const model = gltf.scene;            

            window.mainModelUser = model;

            

            // Auto-scale & center model

            const box = new THREE.Box3().setFromObject(model);

            const size = new THREE.Vector3();

            box.getSize(size);

            const center = new THREE.Vector3();

            box.getCenter(center);



            // Recenter model

            model.position.sub(center);



            // Scale to fit

            const maxDim = Math.max(size.x, size.y, size.z);

            const scale = 2 / maxDim;

            model.scale.setScalar(scale);



            scene.add(model);



            // ✅ Update camera + controls target to model center

            const boxNew = new THREE.Box3().setFromObject(model);

            const centerNew = new THREE.Vector3();

            boxNew.getCenter(centerNew);



            controls.target.copy(centerNew);

            camera.lookAt(centerNew);



            animate();

            setTimeout(() => {

                // updateSvgPatternColors();

                // updateZoneColorPreviews();                

                loadDesignFromDatabaseUSER();

            }, 600);

        });

    }

    // user Model load end


     Promise.all([

        document.fonts.load("16px HaettenschweilerJog4"),

        document.fonts.load("16px JerseyM54Jog3"),

        document.fonts.load("16px NHLEdgeNewYorkIslandersJOG"),

        document.fonts.load("16px SuperstarM54JOG"),

        document.fonts.load("16px BebasNeueJog2"),

        document.fonts.load("16px Brobane"),

        document.fonts.load("16px NHLChicago"),

        document.fonts.load("16px NHLENYR_"),

        document.fonts.load("16px NHLMA__"),

        document.fonts.load("16px PROBLOCK"),

        document.fonts.load("16px BrushScriptStd"),

        document.fonts.load("16px ThirstyScriptExtraBoldDemo"),

        document.fonts.load("16px AngletonScript"),

    ]).then(() => {

        // Fonts are ready — re-render any text decals that were drawn before fonts loaded
        if (window.mainModelUser) {
            updateMeshTextureWithAllDecalsUser();
        }

    }).catch(err => {

        console.error("Font loading failed:", err);

        // Even if font loading fails, attempt to render with fallback fonts
        if (window.mainModelUser) {
            updateMeshTextureWithAllDecalsUser();
        }

    });

    const cameraPositions = {

        frontAngle: { x: 0, y: 0, z: 1 },

        backAngle: { x: 0, y: 0, z: -1 },

        rightAngle: { x: -1, y: 0, z: 0 },

        leftAngle: { x: 1, y: 0, z: 0 }

    };



    function setViewfront(view) {

        console.log("👉 Switching view:", view);



        const position = cameraPositions[view];

        const model = window.mainModel;

        const camera = window.mainCamera;

        if (!position || !model) {

            console.warn("⚠️ Missing model or position:", view);

            return;

        }



        // Get model bounding box & center

        const box = new THREE.Box3().setFromObject(model);

        const center = box.getCenter(new THREE.Vector3());

        const size = box.getSize(new THREE.Vector3());

        const maxDim = Math.max(size.x, size.y, size.z);

        const distance = maxDim * 2; // keep camera outside



        // Move camera relative to center        

        camera.position.set(

            center.x + position.x * distance,

            center.y + position.y * distance,

            center.z + position.z * distance

        );



        // Look at model center

        camera.lookAt(center);



        // Update OrbitControls

        // if (controls) {

        //     controls.target.copy(center);

        //     controls.update();

        // }



        console.log("📷 Camera moved to:", camera.position);

    }



    // Hook buttons

    document.querySelector(".frontAngle").addEventListener("click", () => setViewfront("frontAngle"));

    document.querySelector(".backAngle").addEventListener("click", () => setViewfront("backAngle"));

    document.querySelector(".leftAngle").addEventListener("click", () => setViewfront("leftAngle"));

    document.querySelector(".rightAngle").addEventListener("click", () => setViewfront("rightAngle"));

    
    function updateMeshTextureWithAllDecalsUser() {

        if (!window.mainModelUser ) return;



        const allTextDecals = window.textDecals || [];

        const allImageDecals = window.imageDecals || [];

        const allPatternDecals = window.patternDecals || [];



        function decalMatchesMesh(decal, mesh) {

            if (!decal) return false;

            if (decal.mesh && typeof decal.mesh === "object") {

                return decal.mesh === mesh || decal.mesh.uuid === mesh.uuid || decal.mesh.name === mesh.name;

            }

            if (decal.meshName) return decal.meshName === mesh.name;

            if (decal.meshUuid) return decal.meshUuid === mesh.uuid;

            return false;

        }



        let pendingImageLoads = 0;

        function ensureImageLoaded(img, cb) {

            if (!img) return cb(false);

            if (img.complete && img.naturalWidth !== 0) return cb(true);

            pendingImageLoads++;

            img.onload = () => {

                pendingImageLoads--;

                cb(true);

                setTimeout(updateMeshTextureWithAllDecalsUser, 50);

            };

            img.onerror = () => {

                pendingImageLoads--;

                cb(false);

            };

        }



        window.mainModelUser.traverse(mesh => {

            if (!mesh.isMesh || !mesh.material) return;

            if (!window.MESH_COLORS[mesh.name]|| !mesh) return; 

            const RES = 1024;

            const canvas = document.createElement("canvas");

            canvas.width = RES;

            canvas.height = RES;

            const ctx = canvas.getContext("2d");



            // 1) Background color or gradient

            const meshColor = window.MESH_COLORS[mesh.name] || "#1a0000ff";



            if (mesh.userData && mesh.userData.gradient) {

                const gradient = mesh.userData.gradient;

                const angleRad = THREE.MathUtils.degToRad(gradient.angle || 0);

                const cos = Math.cos(angleRad), sin = Math.sin(angleRad);

                const cx = RES / 2, cy = RES / 2;

                const length = Math.sqrt(cx * cx + cy * cy) * (gradient.scale || 1);

                const g = ctx.createLinearGradient(cx - cos * length, cy - sin * length, cx + cos * length, cy + sin * length);

                const applyAlpha = (hex, a) => {

                    const r = parseInt(hex.slice(1, 3), 16);

                    const g = parseInt(hex.slice(3, 5), 16);

                    const b = parseInt(hex.slice(5, 7), 16);

                    return `rgba(${r},${g},${b},${a})`;

                };

                g.addColorStop(0, applyAlpha(gradient.color1 || meshColor, 0.5));

                g.addColorStop(1, applyAlpha(gradient.color2 || meshColor, 0.5));

                ctx.fillStyle = g;

            } else {

                ctx.fillStyle = meshColor;

            }

            ctx.fillRect(0, 0, RES, RES);



            // 2) Patterns

            const meshPatternDecals = allPatternDecals.filter(d => decalMatchesMesh(d, mesh));

            for (const decal of meshPatternDecals) {

                if (!decal.image) continue;

                ensureImageLoaded(decal.image, ok => {

                    if (!ok) return;

                    ctx.save();

                    ctx.globalAlpha = decal.opacity ?? 1;

                    try {

                        const pattern = ctx.createPattern(decal.image, "repeat");

                        if (pattern.setTransform) pattern.setTransform(new DOMMatrix().scaleSelf(decal.scale || 1, decal.scale || 1));

                        ctx.fillStyle = pattern;

                        ctx.fillRect(0, 0, RES, RES);

                    } catch (err) {

                        const tileW = (decal.image.naturalWidth || decal.image.width) * (decal.scale || 1);

                        const tileH = (decal.image.naturalHeight || decal.image.height) * (decal.scale || 1);

                        for (let x = 0; x < RES; x += tileW) {

                            for (let y = 0; y < RES; y += tileH) {

                                ctx.drawImage(decal.image, x, y, tileW, tileH);

                            }

                        }

                    }

                    ctx.restore();

                });

            }



            // 3) Image decals

            const meshImageDecals = allImageDecals.filter(d => decalMatchesMesh(d, mesh));

            for (const decal of meshImageDecals) {

                if (!decal.image) continue;

                ensureImageLoaded(decal.image, ok => {

                    if (!ok) return;

                    ctx.save();

                    const centerX = RES / 2 + (decal.offset?.x ?? 0) * RES;

                    const centerY = RES / 2 + (decal.offset?.y ?? 0) * RES;

                    ctx.translate(centerX, centerY);

                    ctx.rotate(decal.rotation || 0);

                    ctx.scale(decal.scale || 1, decal.scale || 1);

                    ctx.translate(-centerX, -centerY);

                    const w = (decal.bounds?.originalWidth || decal.image.naturalWidth || decal.image.width) * (decal.scale || 1);

                    const h = (decal.bounds?.originalHeight || decal.image.naturalHeight || decal.image.height) * (decal.scale || 1);

                    ctx.globalAlpha = decal.opacity ?? 1;

                    ctx.drawImage(decal.image, centerX - w / 2, centerY - h / 2, w, h);

                    ctx.globalAlpha = 1;

                    ctx.restore();

                });

            }



            // 4) Text decals

            const meshTextDecals = allTextDecals.filter(d => decalMatchesMesh(d, mesh));

            for (const decal of meshTextDecals) {

                ctx.save();

                const centerX = RES / 2 + (decal.offset?.x ?? 0) * RES;

                const centerY = RES / 2 + (decal.offset?.y ?? 0) * RES;

                ctx.translate(centerX, centerY);

                ctx.rotate(decal.rotation || 0);

                ctx.translate(-centerX, -centerY);

                const fontSize = decal.fontSize || 48;

                const fontFamily = decal.fontFamily || "PROBLOCK";

                ctx.font = `${fontSize}px ${fontFamily}`;

                ctx.textAlign = "center";

                ctx.textBaseline = "middle";

                ctx.fillStyle = decal.color || "#000";

                if (decal.hasOutline && decal.outlineWidth > 0) {

                    ctx.strokeStyle = decal.outlineColor || "#000";

                    ctx.lineWidth = decal.outlineWidth;

                    ctx.strokeText(decal.text, centerX, centerY);

                }

                ctx.fillText(decal.text, centerX, centerY);

                ctx.restore();

            }



            // 5) Apply canvas as texture

            if (pendingImageLoads > 0) return; // wait for images

            const texture = new THREE.CanvasTexture(canvas);

            texture.flipY = false;

            mesh.material.map = texture;

            mesh.material.needsUpdate = true;

        });

    }    

    function loadArtAprovel(){
        const loader = document.getElementById("svgLoader");
        loader.style.display = "flex";   // 🔥 show loader
        const design_id = document.querySelector("input[name='design_id']").value;
        fetch("ajax/generate_artwork_svg.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            design_id: design_id,
            artwork: window.finalArtworkData,
            color: window.MESH_COLORS,
            textDecals: window.textDecals,
            imageDecals: window.imageDecals
        })
        })
        .then(r => r.json())
        .then(res => {
            // document.getElementById("frontPreview").innerHTML = res.svg;
            // const svg = document.querySelector("#frontPreview svg");
            // svg.setAttribute("style", "font-family: JerseyM54Jog3, SuperstarM54JOG, sans-serif");

            const blob = new Blob([res.svg], { type: "image/svg+xml" });
            const url = URL.createObjectURL(blob);

            document.getElementById("frontPreview").innerHTML =
                `<img src="${url}" style="width:100%">`;
            // const base64 = btoa(unescape(encodeURIComponent(res.svg)));

            // document.getElementById("frontPreview").innerHTML =
            // `<img src="data:image/svg+xml;base64,${base64}" style="width:100%;">`;
        }).finally(() => {
            loader.style.display = "none";   // ✅ hide loader always
        });

    }
});

