/**
 * Admin JS.
 *
 * @since 1.0.0
 */

document.addEventListener("DOMContentLoaded", () => {
  const navTabsWrapperClassName = "nav-tab-wrapper";
  const tabContentWrapperClassName = "tabs-holder";
  const activeTabClassName = "nav-tab-active";

  document
    .querySelectorAll(`.${tabContentWrapperClassName} > div`)
    .forEach((element) => {
      element.style.display = "none";
    });

  if (location.hash.substring(1)) {
    document
      .getElementById(location.hash.substring(1))
      .classList.add(activeTabClassName);
    document.getElementById(`${location.hash.substring(1)}-tab`).style.display =
      "block";
  } else {
    document
      .querySelector(`.${navTabsWrapperClassName} a:first-child`)
      .classList.add(activeTabClassName);
    document.querySelector(
      `.${tabContentWrapperClassName} #${document
        .querySelector(`.${navTabsWrapperClassName} a:first-child`)
        .getAttribute("id")}-tab`
    ).style.display = "block";
  }

  document
    .querySelectorAll(`.${navTabsWrapperClassName} a`)
    .forEach((element) => {
      element.addEventListener("click", () => {
        document
          .querySelectorAll(`.${navTabsWrapperClassName} a`)
          .forEach((element) => {
            element.classList.remove(activeTabClassName);
          });
        element.classList.add(activeTabClassName);

        document
          .querySelectorAll(`.${tabContentWrapperClassName} > div`)
          .forEach((element) => {
            element.style.display = "none";
          });
        document.querySelector(
          `.${tabContentWrapperClassName} > #${element.getAttribute("id")}-tab`
        ).style.display = "block";
      });
    });
});

jQuery(document).ready(($) => {
  $("#configuration-fields").submit((event) => {
    event.preventDefault();
    const $configuration = {};

    $("#configuration-fields :input").each((index, element) => {
      const $key = $(element).attr("id");
      const $val = $(element).val();

      $configuration[$key] = $val;
    });

    $configuration.action = "firebase_configs";

    // eslint-disable-next-line no-undef
    $.post(ajaxurl, $configuration, (e) => {
      if (e.success === true) {
        $.toast({
          heading: "Success",
          text: "Config updated.",
          showHideTransition: "slide",
          icon: "success",
          position: {
            top: 40,
            right: 80,
          },
        });
      }
    });
  });

  $("#sign-in-providers-form").submit((event) => {
    event.preventDefault();
    const $signInProviders = [];

    $("#sign-in-providers-form input:checked").each((index, element) => {
      $signInProviders.push($(element).attr("id"));
    });

    $.post(
      // eslint-disable-next-line no-undef
      ajaxurl,
      {
        action: "firebase_providers",
        enabled_providers: $signInProviders,
      },
      (e) => {
        if (e.success === true) {
          $.toast({
            heading: "Success",
            text: "Sign-in providers updated.",
            showHideTransition: "slide",
            icon: "success",
            position: {
              top: 40,
              right: 80,
            },
          });
        }
      }
    );
  });
});
