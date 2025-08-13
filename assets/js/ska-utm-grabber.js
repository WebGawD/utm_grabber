function processUrl() {
  const urlParams = new URLSearchParams(window.location.search);
  const utmParams = utmGrabberData.utmParams;
  let hasUtmParams = false;

  // Check if any UTM parameter exists in the URL
  utmParams.forEach((param) => {
    if (urlParams.has(param)) {
      hasUtmParams = true;
    }
  });

  if (hasUtmParams) {
    // Store UTM parameters in sessionStorage
    sessionStorage.setItem('utmParams', window.location.search);
  }

  const storedParams = new URLSearchParams(sessionStorage.getItem('utmParams'));

  if (storedParams.toString()) {
    // Add UTM parameters to all forms
    addUtmToForms(storedParams);

    // Determine channel and source, and add to forms
    const channelInfo = determineChannelAndSource(storedParams);
    addChannelInfoToForms(channelInfo);
  } else {
    // If no stored params, set channel to Organic and source to Direct
    addChannelInfoToForms({ channel: 'Organic', source: 'Direct' });
  }
}

function addUtmToForms(params) {
  const forms = document.querySelectorAll('form');
  forms.forEach((form) => {
    utmGrabberData.utmParams.forEach((param) => {
      let input = form.querySelector(
        `input[name="form_fields[${param}]"], input[name="${param}"]`,
      );
      // Log to if UMT are being capture and Forms Fields are being
      // console.log('UTM Params:', params.toString());
      // console.log('Form Fields Found:', input ? 'Yes' : 'No');
      if (input) {
        input.value = params.get(param) || '';
      }
    });
  });
}

function determineChannelAndSource(params) {
  let channel = 'Organic';
  let source = '';

  // Check if there are any search params
  if (params.toString()) {
    // Check for utm_source
    const utmSource = params.get('utm_source');
    if (utmSource) {
      // Determine source based on utm_source
      const lowerSource = utmSource.toLowerCase();
      if (lowerSource.includes('google')) {
        source = 'Google';
      } else if (lowerSource.includes('bing')) {
        source = 'Bing';
      } else if (lowerSource.includes('yahoo')) {
        source = 'Yahoo';
      } else {
        source = utmSource; // Use the utm_source value if it's not a recognized search engine
      }

      // Determine channel based on utm_source
      if (lowerSource.includes('cpc') || lowerSource.includes('ppc')) {
        channel = 'Paid Search';
      } else if (
        lowerSource.includes('facebook') ||
        lowerSource.includes('instagram')
      ) {
        channel = 'Social';
      } else if (lowerSource.includes('email')) {
        channel = 'Email';
      } else {
        channel = 'Other';
      }
    }

    // Check for utm_medium
    const utmMedium = params.get('utm_medium');
    if (utmMedium) {
      const lowerMedium = utmMedium.toLowerCase();
      if (lowerMedium.includes('cpc') || lowerMedium.includes('ppc')) {
        channel = 'Paid Search';
      } else if (lowerMedium.includes('social')) {
        channel = 'Social';
      } else if (lowerMedium.includes('email')) {
        channel = 'Email';
      }
    }

    // Check for gclid (Google Click ID)
    if (params.get('gclid')) {
      channel = 'Paid Search';
      source = 'Google';
    }

    // Check for msclkid (Microsoft Click ID)
    if (params.get('msclkid')) {
      channel = 'Paid Search';
      source = 'Bing';
    }

    // Check for utm_campaign
    const utmCampaign = params.get('utm_campaign');
    if (utmCampaign) {
      // You can add additional logic here if needed
    }

    // Check for utm_term
    const utmTerm = params.get('utm_term');
    if (utmTerm) {
      // You can add additional logic here if needed
    }

    // Check for utm_content
    const utmContent = params.get('utm_content');
    if (utmContent) {
      // You can add additional logic here if needed
    }
  }

  return { channel, source };
}

function addChannelInfoToForms(channelInfo) {
  const forms = document.querySelectorAll('form');
  forms.forEach((form) => {
    let channelInput = form.querySelector(
      'input[name="form_fields[channel]"], input[name="channel"]',
    );
    let sourceInput = form.querySelector(
      'input[name="form_fields[source]"], input[name="source"]',
    );

    if (channelInput) {
      channelInput.value = channelInfo.channel;
    }
    if (sourceInput) {
      sourceInput.value = channelInfo.source;
    }
  });
}

document.addEventListener('DOMContentLoaded', function () {
  const observer = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
      if (mutation.type === 'childList') {
        const forms = mutation.target.querySelectorAll('form');
        if (forms.length > 0) {
          processUrl();
        }
      }
    });
  });

  observer.observe(document.body, { childList: true, subtree: true });
});
