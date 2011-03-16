<?php

	/*** Cascade Class - Copyright © Elit 2006 - id: _9_5_1_5e60209_1150902734328_864766_430 ***/

	/** + Class Description +
		*
		* This class provides a cascasde form, providing an easy way for people to invite others to vote.
		**/


	class Cascade
	{
		/*+*+*+*+*+*+*+*+* operations *+*+*+*+*+*+*+*+*/

		/** - return type: void
			*
			* The constructor.
			**/
		function Cascade()
		{//_9_5_1_5e60209_1150903042171_621016_494
		}//_9_5_1_5e60209_1150903042171_621016_494

		/** - return type: void
			* - param title: string
			* 	The title of the cascade.
			* - param message: string
			* 	The message to be shown to the form user.
			* - param formid: string
			* 	The dom id of the form.
			* - param disclaimer: string
			* 	The optional disclaimer for the form.
			*
			* This method prints a cascade form.
			**/
		function printForm($title, $message, $formid, $disclaimer = "")
		{//_9_5_1_5e60209_1150903042171_427736_495
			echo <<<END
			<div style="margin: 0 auto 10px; auto; width: 549px; background-image: url('layout/background_graph.jpg'); border: 1px solid black">
				<h3 style="padding: 0; margin: 5px; color: #ED1C24;">$title </h3>
				<p style="margin: 0 5px 5px 5px; padding: 0">$message</p>
				<div style="width: 90px; float: right">
					<div class="button" style="margin-top: -2px; width: 80px;" onclick="postForm('$formid', showCascadeResult)">
						<img class="button" alt="keuzerondje" src="layout/checkbox_checked.png"/>
						<p class="button">verzend</p>
					</div>
				</div>
				<div>
					<form method="post" action="action.php" id="$formid">
						<p style="margin: 0 5px; padding: 0">
							<b>Emailadres </b>
							<input type="text" style="margin: 0 5px; width: 140px" name="emailaddress" id="emailaddress"/>
							<b>Uw naam</b>
							<input type="text" style="margin: 0 5px; width: 120px" name="sender" id="sender"/>
							<input type="hidden" name="action" value="cascade"/>
						</p>
					</form>
				</div>
				<p style="margin: 5px; padding: 0; color: #666666; font-size: 0.6em; clear: both">$disclaimer</p>
			</div>
END;
		}//_9_5_1_5e60209_1150903042171_427736_495

	}

?>
