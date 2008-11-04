<?php
/**
 *wordpress转typecho meta数据转换(metas,relationship)程序  
 */

$res = mysql_connect("localhost", "root", "123456");
mysql_select_db("program_wordpress");
$tablepre = 'wp_';
$typechoPre = 'typecho_';
mysql_query('SET NAMES utf8');
/**
 *计算term的总数  
 */

$termCount = mysql_result(mysql_query("SELECT COUNT(*) FROM {$tablepre}terms"), 0);
//echo $termCount;
$termRelationshipQuery = mysql_query("SELECT * FROM {$tablepre}term_relationships");
while($termRelationshipInfo = mysql_fetch_array($termRelationshipQuery)) {
    $termTaxonomyInfo = mysql_fetch_array(mysql_query("SELECT * FROM
                {$tablepre}term_taxonomy WHERE term_taxonomy_id =
                '$termRelationshipInfo[term_taxonomy_id]'"));
    $termInfo = mysql_fetch_array(mysql_query("SELECT * FROM {$tablepre}terms WHERE term_id =
                '$termTaxonomyInfo[term_id]'"));
   
    $name = addslashes($termInfo['name']);
    $slug = addslashes($termInfo['slug']);

    if($termTaxonomyInfo['taxonomy'] == 'category') {
        $type = 'category';
    } elseif($termTaxonomyInfo['taxonomy'] == 'link_category') {
/**
 * 这个是link的分类,在typecho里面没有
 *
 */
        $type = 'link_category';
    } else {
        $type = 'tag';
    }
    $description = $termTaxonomyInfo['description'];
    $count =  $termTaxonomyInfo['count'];
    $sort = $termRelationshipInfo['term_order'];
    if($i = mysql_fetch_array(mysql_query("SELECT * FROM {$typechoPre}metas WHERE slug= '$termInfo[slug]' AND type != '$type'"))) {
        $termInfo[term_id] += $termCount;
        $termCount++;
            }
    $mid = $termInfo['term_id'];
    if(!$termArray = mysql_fetch_array(mysql_query("SELECT * FROM {$typechoPre}metas WHERE slug = '$termInfo[slug]' AND type = '$type'"))) {
                mysql_query("INSERT INTO {$typechoPre}metas VALUES('$mid', '$name', '$slug', '$type', '$description', '$count', '$sort')");
    } else {
        $mid = $termArray['mid'];
    }

    mysql_query("INSERT INTO {$typechoPre}relationships
            VALUES('$termRelationshipInfo[object_id]', '$mid')");
    
}

