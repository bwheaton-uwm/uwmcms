/**
 * Custom JavaScript for Provider templates.
 *
 * Include custom JavaScript that should be included
 * every time a provider page loads. Any functions
 * here should operate within the <article.node--res-provider>
 * container.
 *
 * @link https://www.uwmedicine.org/bios/*
 */

(function ($, Drupal, drupalSettings) {

  Drupal.behaviors.uwmLoadProviderPubications = {
    attach(context, settings) {

      const pubMedJournalApi = 'https://eutils.ncbi.nlm.nih.gov/entrez/eutils/esummary.fcgi?db=pubmed&retmode=json&id=';
      const $container = $('article.node-res_provider div#pubmed-journal-results', context);

      if ($container.length) {

        const pubMedIds = $container.data('pubmed-journal-ids');
        if (pubMedIds.length > 0) {

          $.get(`${  pubMedJournalApi  }${  pubMedIds  }`, (data) => {

            const items = [];
            $.each(data.result, (a, b) => {

              items.push(formatApiResult(b));

            });

            $('#publications-tab-tab', context).removeClass('disabled');
            $container.html(` <ul> ${  items.reverse().join('')  } </ul> `);

          });

        }

      }

    }

  };

  const formatApiResult = function (data) {

    let str = '';

    if (data.authors) {

      const authors = [];

      $.each(data.authors, (a, b) => {
        authors.push(b.name);
      });

      str += `${  authors.join(", ")  } `;

    }

    if (data.uid) {

      str += `${  '<a href="http://www.ncbi.nlm.nih.gov/sites/' +
        'entrez?cmd=retrieve&db=PubMed&tool=UWMedicine&dopt=Abstract&list_uids='  }${  data.uid  }" ` +
        `target="_blank">${  data.title  }</a> `;

    }

    if (data.fulljournalname) {
      str += `<em>${  data.fulljournalname  }</em> `;
    }

    if (data.pubdate) {
      str += `${  data.pubdate  }; `;
    }

    if (data.volume) {
      str += `${  data.volume  }; `;
    }

    if (data.issue) {
      str += `${  data.issue  }; `;
    }

    if (data.pages) {
      str += `${  data.pages  };`;
    }

    if (str !== '') {
      return `<li class="pubs">${  str  }</li>`;
    }

  }

})(jQuery, Drupal, drupalSettings);
