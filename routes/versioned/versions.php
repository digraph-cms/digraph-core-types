<?php
$versioned = $package->noun();
$versions = $versioned->availableVersions();
$permissions = $cms->helper('permissions');

echo "<h2>Revision history</h2>";
echo "<form action='" . $this->url($package['noun.dso.id'], 'version-diff', []) . "' method='get'>";
echo "<table id='digraph-revision-history'>";
echo "<tr>";
if (count($versions) > 1) {
    echo "<th colspan=2>Compare</th>";
}
echo "<th>Revision note</th><th>Date</th>";
$url = $versioned->url('add', ['type' => $versioned::VERSION_TYPE]);
if ($canAdd = $permissions->checkUrl($url)) {
    echo "<th style='font-size:1rem;'>";
    echo "<a href='$url' class='row-button row-create-item' title='Add revision'>Add revision</a>";
    echo "</th>";
}
echo "</tr>";
$i = 0;
foreach ($versions as $k => $v) {
    $i++;
    echo "<tr class='revision-row' data-rownum='$i'>";
    // comparison options
    if (count($versions) != 1) {
        if ($i == 1) {
            echo "<td></td>";
        } else {
            echo "<td><input type='radio' class='compare-radio compare-radio-a' value='" . $v['dso.id'] . "' name='a' data-rownum='$i'></td>";
        }
        if ($i == count($versions)) {
            echo "<td></td>";
        } else {
            echo "<td><input type='radio' class='compare-radio compare-radio-b' value='" . $v['dso.id'] . "' name='b' data-rownum='$i'></td>";
        }
    }
    // revision info
    echo "<td><a class='revision-link' href='" . $v->url() . "'>";
    echo $v->name() . "</td>";
    echo "<td>";
    echo $cms->helper('strings')->dateHTML($v->effectiveDate());
    echo "</td>";
    // admin tools
    $editUrl = $v->url('edit');
    $deleteUrl = $v->url('delete');
    $canEdit = $permissions->checkUrl($editUrl);
    $canDelete = $permissions->checkUrl($deleteUrl);
    if ($canAdd || $canEdit || $canDelete) {
        echo "<td style='white-space:nowrap;font-size:1rem;'>";
        if ($canEdit) {
            echo "<a href='" . $editUrl . "' title='edit' class='row-button row-edit'>edit</a>";
        }
        if ($canDelete) {
            echo "<a href='" . $deleteUrl . "' title='delete' class='row-button row-delete'>delete</a>";
        }
        echo "</td>";
    }
    echo "</tr>";
}
echo "</table>";
if (count($versions) > 1) {
    echo "<div class='sticky-block bottom'>";
    echo "<input type='submit' class='cta-button green diff-submit-button' value='Compare selected versions'></div>";
    echo "</form>";
}

?>
<script>
    $(() => {
        var $table = $('#digraph-revision-history');
        var $rows = $table.find('tr.revision-row');
        var updateTable = function() {
            $a = $table.find('input.compare-radio-a:checked');
            $b = $table.find('input.compare-radio-b:checked');
            a = $a.attr('data-rownum');
            b = $b.attr('data-rownum');
            //hide bs after as position
            if (a) {
                $rows.each((i) => {
                    if ($rows.eq(i).attr('data-rownum') >= a) {
                        $rows.eq(i).addClass('hide-b');
                    } else {
                        $rows.eq(i).removeClass('hide-b');
                    }
                });
            }
            //hide as before bs position
            if (b) {
                $rows.each((i) => {
                    if ($rows.eq(i).attr('data-rownum') <= b) {
                        $rows.eq(i).addClass('hide-a');
                    } else {
                        $rows.eq(i).removeClass('hide-a');
                    }
                });
            }
            //highlight selection
            if (a && b) {
                $rows.each((i) => {
                    if ($rows.eq(i).attr('data-rownum') >= b && $rows.eq(i).attr('data-rownum') <=
                        a) {
                        $rows.eq(i).addClass('highlighted');
                    } else {
                        $rows.eq(i).removeClass('highlighted');
                    }
                });
                $('.diff-submit-button').attr('disabled',null);
            }else {
                $('.diff-submit-button').attr('disabled',true);
            }
        }
        updateTable();
        $table.find('input.compare-radio').change(updateTable);
    });
</script>
