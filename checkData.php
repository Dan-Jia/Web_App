<?php

function checkTime($rowNr, $row, $start, $stop, $metaData)
{

    // error: the date has not a right format
    if (strtotime($row[$start])) {
        if (strtotime($row[$stop])) {
            //error: stoptime < starttime
            if ($row[$stop]<$row[$start]) {
                print 'in row '.$rowNr.' is '.$metaData['columns'][$start].
          ' smaller than '. $metaData['columns'][$stop].'<br />';
                return false;
            }
        } else {
            print $metaData['columns'][$stop]. " in row". $rowNr .
        " is not date". "<br />";
            return false;
        }//else
    } else {
        print $metaData['columns'][$start]. " in row ". $rowNr .
      " is not date". "<br />";
        return false;
    }//else

    return true;
}

function checkRow($rowNr, $row, $metaData, $checkRowInfo)
{

    //error: Missing data
    if (sizeof($row)<$checkRowInfo['rowSize']) {
        print 'Missing data in row '.$rowNr.'<br />';
        return false;
    }//if

    //error: the information of dates is not correct
    if (($checkRowInfo['start0']) and ($checkRowInfo['stop0'])) {
        if (!checkTime($rowNr, $row, $checkRowInfo['start0'], $checkRowInfo['stop0'], $metaData)) {
            return false;
        }
    }

    if (($checkRowInfo['start1']) and ($checkRowInfo['stop1'])) {
        if (!checkTime($rowNr, $row, $checkRowInfo['start1'], $checkRowInfo['stop1'], $metaData)) {
            return false;
        }
    }

    // error: top0<mid0<bottom0
    if (($checkRowInfo['top0']) and ($checkRowInfo['mid0']) and ($checkRowInfo['bottom0'])) {
        if (($row[$checkRowInfo['top0']] < $row[$checkRowInfo['mid0']]) or ($row[$checkRowInfo['mid0']] < $row[$checkRowInfo['bottom0']])) {
            print "one of the number for top0/mid0/bottom0 in row ".$rowNr. " is not correct". "<br />";
            return false;
        }
    }//if

    // error: chlorophyllmax0<chlorophyllmin0
    if (($checkRowInfo['chlorophyllmax0']) and ($checkRowInfo['chlorophyllmin0'])) {
        if ($row[$checkRowInfo['chlorophyllmax0']] < $row[$checkRowInfo['chlorophyllmin0']]) {
            print "in row ".$rowNr." is chlorophyllmax0 smaller than chlorophyllmin0". "<br />";
            return false;
        }
    }//if

    // error: sedimentmax0<sedimentmin0
    if (($checkRowInfo['sedimentmax0']) and ($checkRowInfo['sedimentmin0'])) {
        if ($row[$checkRowInfo['sedimentmax0']]< $row[$checkRowInfo['sedimentmin0']]) {
            print "in row ".$rowNr." is sedimentmax0 smaller than sedimentmin0". "<br />";
            return false;
        }
    }//if

    // error: gelbstoffmax0<gelbstoffmin0
    if (($checkRowInfo['gelbstoffmax0']) and ($checkRowInfo['gelbstoffmin0'])) {
        if ($row[$checkRowInfo['gelbstoffmax0']] < $row[$checkRowInfo['gelbstoffmin0']]) {
            print "in row ".$rowNr." is gelbstoffmax0 smaller than gelbstoffmin0". "<br />";
            return false;
        }
    }//if

    return true;
}
