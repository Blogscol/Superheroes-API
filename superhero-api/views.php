<?php
class View_Functions
{
	const API_ENDPOINT = 'https://www.superheroapi.com/api.php';

	const TOKEN = '10166128300335082';

	public static function get_superheroe_by_name($name)
	{
	    $dataHero = get_transient('superhero_name_'.$name);

	    if (false === $dataHero)
	    {
	        try
	        {
				$dataHero = file_get_contents(self::API_ENDPOINT . '/' . self::TOKEN . '/search/' .$name);

	            set_transient('superhero_name_'.$name, $dataHero, 86400 );
	        }
	        catch (Exception $e)
	        {
	            $dataHero = false;
	        }
	    }

	    return $dataHero;
	}

	public static function list_characters_shortcode($content, $data)
	{
		$arraySuperHero = array();

		if(isset($data['name']))
		{
			foreach (explode(",",$data['name']) as $key => $value)
			{
				$dataJson = json_decode(View_Functions::get_superheroe_by_name($value), true);

				if($dataJson["response"] = "success")
				{
					foreach ($dataJson["results"] as $key => $superheroData)
					{
						$arraySuperHero[] = $superheroData;
					}
				}
			}
		}

		ob_start();
		?>
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12"><p class="font-weight-bold pt-2 mb-1 text-center"><?php echo $content; ?></p></div>

					<div class="col-md-12">
						<div class="row no-gutters">
						<?php
							echo '<div class="list-group">';
							foreach ($arraySuperHero as $key => $superhero)
							{
								self::list_character_view($superhero);
							}
							echo '</div>';
						?>
						</div>
					</div>
				</div>
			</div>
		<?php
		return ob_get_clean();
	}

	public static function list_character_view($superheroData)
	{
		if($superheroData["biography"]["alignment"] == "bad")
			$ribbon = "red";
		else
			$ribbon = '';
		?>
        <div class="list-group-item">
	            <div class="row">
	                <div class="col-md-12 col-12">
	                    <div class="row box">
	                    	<div class="ribbon <?php echo $ribbon; ?>"><span><?php echo $superheroData["biography"]["alignment"]; ?></span></div>
	                        <div class="col-md-4 user-img pt-1">
							  <img src="<?php echo $superheroData["image"]["url"]; ?>">
	                        </div>
	                        <div class="col-md-8">
	                            <p class="font-weight-bold pt-2 mb-1 text-center text-md-left listText"><?php echo $superheroData["name"]; ?></p>
	                            <div class="user-detail row">
	                                <div class="col-md-12"><?php echo $superheroData["biography"]["full-name"]; ?></div>
	                                <div class="col-sm-6">
										<p class="text-left" style="width: 50%;display:inline;">Intelligence: <?php echo $superheroData["powerstats"]["intelligence"]; ?></p>
									</div>
	                                <div class="col-sm-6">
										<p class="text-right" style="width: 50%;display:inline;">Strength: <?php echo $superheroData["powerstats"]["strength"]; ?></p>
	                                </div>
	                                <div class="col-sm-6">
										<p class="text-left" style="width: 50%;display:inline;">Speed: <?php echo $superheroData["powerstats"]["speed"]; ?></p>
									</div>
	                                <div class="col-sm-6">
										<p class="text-right" style="width: 50%;display:inline;">Durability: <?php echo $superheroData["powerstats"]["durability"]; ?></p>
									</div>
	                                <div class="col-sm-6">
										<p class="text-left" style="width: 50%;display:inline;">Power: <?php echo $superheroData["powerstats"]["power"]; ?></p>
									</div>
	                                <div class="col-sm-6">
										<p class="text-right" style="width: 50%;display:inline;">Combat: <?php echo $superheroData["powerstats"]["combat"]; ?></p>
									</div>
	                            </div>
	                        </div>
	                    </div>
	                </div>
	            </div>
        </div>
		<?php
	}

	public static function single_character_view($postID)
	{
		$imageID = get_post_meta($postID, 'second_featured_img', true);

		if($imageID)
		{
			$singleRow = "singleRowIMG";
			$singleInfo = "singleInfoIMG";

			$image_attributes = wp_get_attachment_image_src( $imageID, "full" )
			?>
				<div class="col-md-12 col-12 imageFull">
					<?php echo '<img class="img-responsive card-img-top" src="' . $image_attributes[0] . '">'; ?>
				</div>
			<?php			
		}
		else
		{
			$singleRow = "singleRow";
			$singleInfo = "singleInfo";
		}

		?>
            <div class="col-md-12 col-12 backgroundFill">
                <div class="row <?php echo $singleRow;?>">
                    <div class="col-md-3">
                    </div>

                    <div class="col-md-1 user-img pt-1">
					  <?php echo get_the_post_thumbnail($postID, 'medium', array( 'class' => ' card-img-top' )); ?>
                    </div>
                    <div class="col-md-8">
                        <p class="font-weight-bold pt-2 mb-1 text-center text-capitalize text-md-left singleTitleText"><?php echo get_the_title($postID); ?></p>

                        <p class="font-weight-bold pt-2 mb-1 text-center text-capitalize text-md-left singleTitleText"><?php echo get_post_meta($postID, 'type', true); ?></p
                    </div>
                </div>
            </div>

            <div class="col-md-12 col-12 backgroundFill">
                <div class="row">            
	            	<div class="col-md-3">
	            	</div>

	            	<div class="<?php echo $singleInfo;?> col-md-6">
                        <p class="font-weight-bold pt-2 mb-1 text-capitalize">Biography</p>
	            		<p><?php echo get_post_meta($postID, 'bio', true); ?></p>
                        
                        <p class="font-weight-bold pt-2 mb-1 text-capitalize">Last Action</p>
	            		<p><?php echo get_post_meta($postID, 'lastAction', true); ?></p>
                        
                        <p class="font-weight-bold pt-2 mb-1 text-capitalize">Source Of Their Powers</p>
	            		<p><?php echo get_post_meta($postID, 'sourcePowers', true); ?></p>

                        <p class="font-weight-bold pt-2 mb-1 text-capitalize">Weaknesses</p>
	            		<p><?php echo get_post_meta($postID, 'weaknesses', true); ?></p>
	            	</div>

	            	<div class="col-md-3">
	            	</div>
	            </div>
			</div>

		<?php
	}


	public static function excerpt($title, $cutOffLength)
	{
	    $charAtPosition = "";
	    $titleLength = strlen($title);

	    do
	    {
	        $cutOffLength++;
	        $charAtPosition = substr($title, $cutOffLength, 1);
	    } while ($cutOffLength < $titleLength && $charAtPosition != " ");

	    return substr($title, 0, $cutOffLength) . '...';
	}
}
?>