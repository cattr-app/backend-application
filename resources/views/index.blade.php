<!DOCTYPE html>
<html lang="en" class="h-full">
    <head>
        <meta charset="UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
        <title>Cattr</title>
        @vite
    </head>
    <body class="h-full">
        <div id="loader" style="position:fixed;z-index:100;top:0;left:0;right:0;bottom:0;display:grid;background:gainsboro;place-items:center;transition:opacity ease-out .5s">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
             viewBox="0 0 163 163" shape-rendering="geometricPrecision" text-rendering="geometricPrecision" style="width:15rem">
            <style>
                #bg {
                    animation: bg_c_o 3000ms linear infinite normal forwards
                }
                @keyframes bg_c_o {
                    0% {opacity: 0}
                    16.666667% {opacity: 1}
                    100% {opacity: 1}
                }
                #sector {
                    animation: sector_c_o 3000ms linear infinite normal forwards
                }
                @keyframes sector_c_o {
                    0% {opacity: 0}
                    33.333333% {opacity: 0}
                    50% {opacity: 1}
                    100% {opacity: 1}
                }
                #cat {
                    animation: cat_c_o 3000ms linear infinite normal forwards
                }
                @keyframes cat_c_o {
                    0% {opacity: 0} 66.666667% {opacity: 0} 83.333333% {opacity: 1} 100% {opacity: 1}
                }
            </style>
            <g mask="url(#main_mask)">
                <g>
                    <path id="bg"
                          d="M81.5,163c45.011,0,81.5-36.489,81.5-81.5C163,36.4888,126.511,0,81.5,0C36.4888,0,0,36.4888,0,81.5C0,126.511,36.4888,163,81.5,163Z"
                          opacity="0" fill="#151941"/>
                    <path id="sector" d="M145.043,81.5012c0-35.0831-28.44-63.5237-63.5235-63.5237v63.5237h63.5235Z"
                          opacity="0" fill="#837ad4"/>
                    <g id="cat" transform="translate(.000001 0)" opacity="0">
                        <path d="M57.6688,113.657h47.6562l18.386,65.553h-84.428l18.3858-65.553Z" fill="#fff"/>
                        <path d="M126.701,103.649c0,23.509-23.476,35.871-45.2399,35.871-21.7635,0-45.2399-12.362-45.2399-35.871c0-23.5096,23.4764-37.3416,45.2399-37.3416c21.7639,0,45.2399,13.832,45.2399,37.3416Z"
                              fill="#151941"/>
                        <path d="M45.171,66.3063c-.0236,4.5515,1.3018,14.4041,1.8365,16.927L63.9466,67.3136c-1.0101-1.4229-2.7827-4.0948-6.3105-8.2921s-7.2257-7.0983-9.5783-6.1659-2.8514,6.5997-2.8868,13.4507Z"
                              fill="#fff"/>
                        <path d="M117.696,66.3063c.024,4.5518-1.301,14.4048-1.835,16.9278L98.9217,67.3125c1.0099-1.4229,2.7823-4.0948,6.3093-8.2921c3.528-4.1973,7.225-7.0983,9.578-6.1657c2.352.9326,2.851,6.6002,2.887,13.4516Z"
                              fill="#fff"/>
                        <path d="M126.701,101.21c0,23.509-23.476,31.849-45.2399,31.849-21.7635,0-45.2399-8.34-45.2399-31.849c0-23.5095,23.4764-37.3416,45.2399-37.3416c21.7639,0,45.2399,13.8321,45.2399,37.3416Z"
                              fill="#fff"/>
                        <path d="M83.722,96.1965c0-.0009,0-.0017,0-.0026c0-1.2203-.9756-2.2095-2.1791-2.2095-1.1099,0-2.0259.8412-2.1618,1.929-.0644.2552-.128.5161-.1926.7809v.0001.0001c-.4234,1.7361-.8877,3.6405-1.8719,5.1905-1.6804,2.647-4.0113,2.922-4.921,2.872l-.2149,3.966c1.8844.103,5.8011-.508,8.4618-4.699.3315-.523.6204-1.075.8737-1.636.2534.561.5423,1.113.8738,1.636c2.6607,4.191,6.5774,4.802,8.4618,4.699l-.2149-3.966c-.9097.05-3.2406-.225-4.921-2.872-.9842-1.55-1.4486-3.4544-1.8719-5.1905l-.0001-.0003-.0001-.0004c-.0408-.1675-.0813-.3334-.1218-.4973Z"
                              clip-rule="evenodd" fill="#151941" fill-rule="evenodd"/>
                        <ellipse rx="3.68349" ry="5.56042" transform="translate(59.8336 89.5485)" fill="#151941"/>
                        <ellipse rx="3.68349" ry="5.56042" transform="translate(103.031 89.5477)" fill="#151941"/>
                    </g>
                </g>
                <mask id="main_mask" mask-type="alpha">
                    <path d="M81.5,163c45.011,0,81.5-36.489,81.5-81.5C163,36.4888,126.511,0,81.5,0C36.4888,0,0,36.4888,0,81.5C0,126.511,36.4888,163,81.5,163Z"
                          fill="#f6f5fa"/>
                </mask>
            </g>
        </svg>
        </div>
        <div id="app"></div>
    </body>
</html>
