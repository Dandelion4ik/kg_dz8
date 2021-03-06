<HTML lang="ru">

<BODY>
<canvas id="hw_08" width="500" height="400" style="border:1px solid">
</canvas>
<script>
    function Mult_Mv(M, v) {
        const res = [];
        for (let i = 0; i < 4; ++i) {
            res.push(0);
            for (let j = 0; j < 4; ++j) {
                res[i] = res[i] + M[i * 4 + j] * v[j];
            }
        }
        return res;
    }
</script>
<script>
    const canvas = document.getElementById("hw_08");
    const ctx = canvas.getContext("2d");
    canvas.setAttribute("tabindex", 0);
    const asd = 0;
    let angle = 0;
    const facets = [
        [0, 1, 5],
        [1, 2, 5],
        [2, 3, 5],
        [3, 4, 5],
        [4, 0, 5],
    ];
    setInterval(draw, 0.2)
    setInterval(clr, 1)
    function clr() {
        ctx.clearRect(0, 0, canvas.width, canvas.height)
    }
    function draw() {
        let out;
        let i;
        const alpha = -angle * Math.PI / 180;

        const M_z = [Math.cos(alpha), -1 * Math.sin(alpha), 0, 0,
            Math.sin(alpha), Math.cos(alpha), 0, 0,
            0, 0, 1, 0,
            0, 0, 0, 1];

        const points = [[30, 30, 0], [130, 30, 0], [130, 100, 0],
            [100, 130, 0], [30, 130, 0], [80, 80, 160]];

        const basisPoint = [80, 80, 0];
        for (i = 0; i < points.length; ++i) {
            points[i][0] -= basisPoint[0]
            points[i][1] -= basisPoint[1]
            out = Mult_Mv(M_z, [points[i][0], points[i][1], points[i][2], 1]);
            points[i][0] = out[0] + basisPoint[0] + 150
            points[i][1] = out[1] + basisPoint[1]
        }

        const alphaX = 75 * Math.PI / 180;
        const M_x = [1, 0, 0, 0,
            0, Math.cos(alphaX), -1 * Math.sin(alphaX), 0,
            0, Math.sin(alphaX), Math.cos(alphaX), 0,
            0, 0, 0, 1];


        for (i = 0; i < points.length; ++i) {
            points[i][0] -= basisPoint[0]
            points[i][1] -= basisPoint[1]
            points[i][2] -= basisPoint[2]
            out = Mult_Mv(M_x, [points[i][0], points[i][1], points[i][2], 1]);
            points[i][0] = out[0] + basisPoint[0]
            points[i][1] = out[1] + basisPoint[1] + 150
            points[i][2] = out[2] + basisPoint[2]
        }

        function fill(points) {
            let min_y = Math.floor(points[0][1]), max_y = Math.floor(points[0][1]);
            for (let i = 0; i < points.length; ++i) {
                if (Math.floor(points[i][1]) < min_y) min_y = Math.floor(points[i][1]);
                if (Math.floor(points[i][1]) > max_y) max_y = Math.floor(points[i][1]);
            }

            let y_array = [];
            for (let j = min_y; j < max_y + 1; ++j) {
                y_array[j] = [];
            }
            for (let i = 0; i < points.length; ++i) {
                let next = 0;
                if (i !== points.length - 1)
                    next = i + 1;
                let up = 0, down = 0;
                if (Math.floor(points[i][1]) > Math.floor(points[next][1])) {
                    up = i;
                    down = next;
                }
                else if (Math.floor(points[i][1]) < Math.floor(points[next][1])) {
                    up = next;
                    down = i;
                }
                else continue;

                let k = (points[up][1] - points[down][1]) / (points[up][0] - points[down][0]);
                for (let j = points[down][1]; j < points[up][1]; ++j) {
                    y_array[Math.floor(j)].push((j - points[down][1]) / k + points[down][0]);
                }
            }

            for (let y = min_y; y < max_y; ++y) {
                let x_array = y_array[y].sort(function (a, b) { return a - b; });
                for (let j = 0; j < x_array.length / 2; ++j) {
                    for (let x = x_array[j * 2]; x < x_array[j * 2 + 1]; ++x) {
                        ctx.fillRect(Math.floor(x), Math.floor(y), 1, 1);
                    }
                }
            }
        }
        const lamp = [-100, -100, 100];

        function cosinus(v1, v2) {
            return (v1[0] * v2[0] + v1[1] * v2[1] + v1[2] * v2[2])
                / (Math.sqrt(Math.pow(v1[0], 2) + Math.pow(v1[1], 2) + Math.pow(v1[2], 2))
                    * Math.sqrt(Math.pow(v2[0], 2) + Math.pow(v2[1], 2) + Math.pow(v2[2], 2)));
        }

        for (let i = 0; i < facets.length; ++i) {
            const p0 = points[facets[i][0]];
            const p1 = points[facets[i][1]];
            const p2 = points[facets[i][2]];
            let center_1_2 = [(p2[0] + p1[0]) / 2, (p2[1] + p1[1]) / 2, (p2[2] + p1[2]) / 2];
            let center = [(center_1_2[0] + p0[0]) / 2, (center_1_2[1] + p0[1]) / 2, (center_1_2[2] + p0[2]) / 2];
            const light = [
                lamp[0] - center[0],
                lamp[1] - center[1],
                lamp[2] - center[2]
            ];
            const normal = [
                (p1[1] - p0[1]) * (p2[2] - p0[2]) - (p2[1] - p0[1]) * (p1[2] - p0[2]),
                (p2[0] - p0[0]) * (p1[2] - p0[2]) - (p1[0] - p0[0]) * (p2[2] - p0[2]),
                (p1[0] - p0[0]) * (p2[1] - p0[1]) - (p2[0] - p0[0]) * (p1[1] - p0[1])
            ];
            const observer = [0, 0, -1];
            if (cosinus(observer, normal) <= 0) {
                cosinus(light, normal);
                ctx.fillStyle = '#8B0000';
                fill([p0, p1, p2])
            }

        }
        if (angle === 360) {
            angle = 0
        } else {
            angle += 1
        }
    }
</script>
</BODY>

</HTML>