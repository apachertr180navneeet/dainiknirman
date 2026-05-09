<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <title>Certificate of Achievement</title>
  <link href="http://fonts.googleapis.com/css?family=Hind:400,700&subset=devanagari,latin-ext" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

  <style>
    @font-face {
        font-family: 'NotoSansDevanagari-Regular';
        src: url('{{ public_path("fonts/Noto_Sans_Devanagari/static/NotoSansDevanagari-Regular.ttf") }}') format("truetype");
        /* src: url('{{ storage_path("storage/fonts/Hind/Hind-Regular.ttf") }}') format("truetype"); */
    }
    /* @font-face {
        font-family: 'Hind';
        src: url('{{ storage_path("fonts/Hind/Hind-Regular.ttf") }}') format("truetype");
    } */
    @font-face {
        font-family: 'Baloo2-Regular';
        src: url('{{ storage_path("fonts/Baloo_2/static/Baloo2-Regular.ttf") }}') format("truetype");
    }
    @font-face {
        font-family: 'Mangal Regular';
        src: url('{{ storage_path("fonts/mangal-regular/Mangal Regular.ttf") }}') format("truetype");
    }
    
    body {
      /* font-family: 'Georgia', serif; */
      /* font-family: 'NotoSansDevanagari-Regular'; */
      font-family: 'Hind', 'Mangal Regular', 'Baloo2-Regular', sans-serif;
      display: flex;
      /* justify-content: center; */
      /* align-items: center; */
      height: 100vh;
      margin: 0;
      background: #f8f8f8;
    }

    .certificate {
      /* width: 900px;
      height: 600px; */
      width: 1100px;
      /*height: 640px;*/
      background: url('{{$certificateBgImagePath}}');
      background-position: center;
      background-repeat: no-repeat;
      background-size: cover;
      text-align: center;
      /* box-shadow: 0 0 20px rgba(0, 0, 0, 0.1); */
      position: relative;
      padding: 40px;
    }

    .logo {
      position: absolute;
      left: 20px;
      top: 20px;
    }

    .logo img {
      width: 120px;
    }

    .subtitle {
      font-size: 18px;
      margin-top: 120px;
      color: #7f8c8d;
    }

    .recipient {
      font-size: 28px;
      margin: 20px 0;
      color: #34495e;
    }

    .description {
      font-size: 16px;
      margin: 20px 0;
      color: #141414;
      line-height: 30px;
    }

    .footer {
      display: flex;
      justify-content: space-between;
      margin-top: 40px;
      padding: 0 50px;
      font-size: 16px;
      font-weight: bold;
    }

    .footer p {
      line-height: 25px;
      color: black;
    }

    .date {
      font-size: 18px;
      font-weight: bold;
      color: #ff7700;
      margin-top: 20px;
    }
  </style>
</head>

<body>
  <div class="certificate" id="certificate">
    <!-- Logo -->
    <!-- <div class="logo">
      <img src="{{$logo}}" alt="Dainik Nirman Logo">
    </div> -->

    <p class="subtitle">TO BE A WINNER - निर्माण काव्य प्रतियोगिता</p>

    <!-- <h2 class="recipient">अधिवक्ता डॉ. गोविंद कुशवाहा</h2> -->
    <h2 class="recipient">{{$contest->author->name ?? ''}}</h2>

    @php
    $rankText = [
      1 => 'प्रथम',
      2 => 'द्वितीय',
      3 => 'तृतीय'
    ];
    @endphp

    <div class="description">
      यह प्रमाण पत्र विजेता को उनकी अद्वितीय प्रतिभा एवं उत्कृष्ट प्रदर्शन के लिए प्रदान किया जाता है। <br>
      उन्होंने <b>"दैनिक निर्माण"</b> POWERed by <b>निरमा प्रकाशन</b> द्वारा आयोजित <br> [ विषय – "{{$contest->contest_title}}" ]
      प्रतियोगिता में {{$rankText[$contest->rank]}} स्थान प्राप्त कर अपनी योग्यता का प्रमाण दिया है। <br>
      हम उनकी इस उपलब्धि के लिए उन्हें हार्दिक बधाई एवं उज्ज्वल भविष्य की शुभकामनाएँ देते हैं।
    </div>

    <div class="date">
      {{date("d F, Y", strtotime($contest->contest_date))}}
    </div>

    <div class="footer">
      <p>जसराज बिश्नोई <br> Chief Editor <br> दैनिक निर्माण</p>
      <p><img src="{{$logo}}" alt="Dainik Nirman Logo" style="width:120px;"></p>
      <p>निरमा बिश्नोई <br> Co-Founder <br> निरमा प्रकाशन</p>
    </div>
  </div>
</body>

</html>