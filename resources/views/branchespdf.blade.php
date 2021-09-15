<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Client Branches Report</title>
</head>
<body>
    <div style="margin-bottom:25px;"><strong>Client: {{$names}} <br/><br/> Year: {{$endDate}} </strong></div>
   
    <table style="border-collapse: collapse;">
        <thead>
            <tr>
                <th style="border-bottom:2pt solid black;">
                    <strong>S.No</strong>
                </th>
                <th style="border-bottom:2pt solid black;">
                    <strong>Bank - Branch Name</strong>
                </th>
            </tr>
        </thead>
        <tbody>
        <?php $number = 1; ?>
        @foreach ($confirmation as $confirm)
        <tr>
            <td style="border-bottom:1pt solid black;">
                <!-- {{$confirm['id']}} -->
                {{ $number }}
            </td>
            <td style="border-bottom:1pt solid black;">
                {{$confirm['branch']}}
            </td>
            <?php $number++; ?>
        </tr>
        @endforeach
        
        </tbody>
    </table>
    
    
</body>
</html>
