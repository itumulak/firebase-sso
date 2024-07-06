import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from "@wordpress/components";

import '../../src/View/Frontend/assets/styles/login.css';
import facebookIconSvg from "../../src/assets/images/facebook-logo.svg";
import googleIconSvg from "../../src/assets/images/google-logo.svg";
import loginScreenshot from "../../src/assets/images/login.png";

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */
export default function Edit( { attributes, setAttributes } ) {
	const { showGoogle, showFacebook } = attributes;

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Firebase Sign-on Settings', 'firebase-sso')}>
					<ToggleControl
						checked={ !! showGoogle }
						label={ __('Google', 'firebase-sso') }
						onChange={() => setAttributes({showGoogle: !showGoogle})}
					/>
					<ToggleControl
						checked={ !! showFacebook }
						label={ __('Facebook', 'firebase-sso') }
						onChange={() => setAttributes({showFacebook: !showFacebook})}
					/>
				</PanelBody>
			</InspectorControls>

			<div {...useBlockProps()}>
				<div style={{maxWidth: "320px", marginLeft: "auto", marginRight: "auto"}}>
					{ showGoogle && (
						<p className="btn-wrapper">
							<button
								id="wp-firebase-google-sign-in"
								className="btn btn-lg btn-google btn-block text-uppercase"
								type="submit"
							>
								<img width={24} src={googleIconSvg} /> Google
							</button>
						</p>
					)}

					{ showFacebook && (
						<p className="btn-wrapper">
							<button
								id="wp-firebase-facebook-sign-in"
								className="btn btn-lg btn-facebook btn-block text-uppercase"
								type="submit"
							>
								<img width={48} src={facebookIconSvg} /> Facebook
							</button>
						</p>
					)}

					<p>
						<img src={loginScreenshot}/>
					</p>
				</div>
			</div>
		</>
	);
}
