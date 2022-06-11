<?php

$site = array(
    'home' => 'static/index.html',
    'admin' => 'static/admin.html'
);
$db_path = 'static/db.json';
$db = json_decode(file_get_contents($db_path), true);

function knn(&$array)
{
    $res = key($array);
    next($array);

    return $res;
}


switch (knn($_GET))
{
    case 'api':
        switch (knn($_GET))
        {
            case 'rks_calc':
                for ($i = $rks_sum = 0; $i < 20 && $i < count($_POST['data']); $i++)
                {
                    $decode = explode("$", $_POST['data'][$i]);
                    $diff = array('EZ' => 0, 'HD' => 1, 'IN' => 2, 'AT' => 3)[$decode[1]];
                    if ((float)$decode[2] >= 70)
                    {
                        foreach ($db as $value)
                            if ($value['name'] == $decode[0])
                            {
                                if ((float)$decode[2] == 100)
                                    $rks_single = (float)$value['rank'][$diff];
                                else
                                    $rks_single = pow(((float)$decode[2] - 55) / 45, 2) * (float)$value[$diff];
                            }
                    }
                    else
                        $rks_single = 0;
                    $rks_sum += $rks_single;
                }
                echo $rks_sum / 20;
                break;

            case 'rank':
                if (!isset($_POST['name']))
                    echo json_encode($db);
                else
                {
                    $res = [];
                    foreach ($db as $value)
                        if (stristr($value['name'], $_POST['name']))
                            $res[] = $value;
                    echo json_encode($res);
                }
                break;
        }
        break;

    case 'admin':
        switch (knn($_GET))
        {
            case 'rank_update':
                if ($_POST['rank'] == 'rm')
                {
                    foreach ($db as $k => $v)
                        if ($v['name'] == $_POST['name'])
                            unset($db[$k]);
                }
                else
                {
                    for ($i = 0, $flag = false; $i < count($db); $i++)
                        if ($_POST['name'] == $db[$i]['name'])
                        {
                            $db[$i]['rank'] = explode(',', $_POST['rank']);
                            $flag = true;
                            break;
                        }
                    if (!$flag)
                        $db[] = array('name' => $_POST['name'], 'rank' => explode(',', $_POST['rank']));
                }
                file_put_contents($db_path, json_encode($db), LOCK_EX);
                header("refresh:0;url='index.php?static&admin'");
                break;
        }
        break;

    case 'static':
        switch (knn($_GET))
        {
            case 'admin':
                echo file_get_contents($site['admin']);
                break;
        }
        break;

    default:
        echo file_get_contents($site['home']);
        break;
}
