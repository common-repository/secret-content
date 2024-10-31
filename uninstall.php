<?php /* this removes the postmeta entrys */
global $wpdb;
$removesecret = $wpdb->query("DELETE FROM $wpdb->postmeta WHERE meta_key = '_secret_new_field'");
if ($removesecret)
{
  // awsome
}
else
{
  // whatever
}
?>