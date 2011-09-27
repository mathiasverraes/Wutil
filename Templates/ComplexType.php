namespace <?php print $namespace ?>;

class <?php print $class; ?> 
{
<?php foreach($members as $member): ?>
  /**
   * @var <?php print $member->getType(); ?> 
   **/
  protected $<?php print $member->getName(); ?>;
<?php endforeach; ?>
<?php foreach($members as $member): ?>
  /**
   * Sets <?php print $member->getName() ?> 
   * @param <?php print $member->getType() ?> $<?php print $member->getName() ?> 
   */
  public function set<?php print ucfirst($member->getName()); ?>($<?php print $member->getName(); ?>)
  {
    $this-><?php print $member->getName(); ?> = $<?php print $member->getName(); ?>;
  }

<?php endforeach; ?>
<?php foreach($members as $member): ?>
  /**
   * Gets <?php print $member->getName() ?> 
   * @return <?php print $member->getType() ?> 
   */
  public function get<?php print ucfirst($member->getName()); ?>()
  {
    return $this-><?php print $member->getName(); ?>;
  }
  
<?php endforeach; ?>
}