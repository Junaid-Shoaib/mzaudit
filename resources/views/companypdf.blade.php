<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Client Branches Report</title>

    <style type="text/css">
        @page {margin: 20px;}
        body {margin: 10px;}
        * {font-family: Verdana, Arial, sans-serif;}
        a {text-decoration: none;}
        table {font-size: x-small;}
        tfoot tr td {font-weight: bold;font-size: large;}
        .information {background-color: #fff;}
        .information .logo {margin: 5px;}
        .information table {padding: 10px;}
        tr:nth-child(even) {background-color: #f2f2f2;}
    </style>
</head>
<body>

<?php
  $dt = \Carbon\Carbon::now(new DateTimeZone('Asia/Karachi'))->format('M d, Y - h:m a');
  ?>


<div class="information">
    <table width="100%">
        <tr>
            <td align="left" style="width: 70%;">
                <h3>Clients</h3>
                {{-- <h3>Client: {{$names}} --}}
                {{-- <br> Year End: {{$endDate}}</h3> --}}
            </td>
            <td align="center">
            </td>
            <td align="right" style="width: 30%;">
                <h5>
                    Generated on: {{ $dt}}
                </h5>
            </td>
        </tr>
    </table>
</div>

<br />

<div class="information">
    <table width="100%" style="border-collapse: collapse;">
            <thead Style="background-color: #a8b3b8;">

            </style>>
        <tr>
            <th style="width: 15%;border-bottom:2pt solid black;">
                <strong>S.No</strong>
            </th>
            <th style="width: 15`%;border-bottom:2pt solid black;">
                <strong>Client Name</strong>
            </th>
            <th style="width: 15`%;border-bottom:2pt solid black;">
                <strong>Fiscal</strong>
            </th>
        </tr>
            </thead>
            <tbody>

		<?php $number = 1; ?>
         @foreach ($company as $comp)

        <tr>
            <td style="width: 15%; text-align: center">
                 {{ $number }}
            </td>
            <td style="width: 70%;">
                  {{$comp->name}}
            </td>
            <td style="width: 15%; text-align: center">
                {{$comp->fiscal}}
          </td>
             <?php $number++; ?>
       </tr>
        @endforeach
            </tbody>

    </table>
</div>






</body>
</html>
