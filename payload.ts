const payload1 = {
    origin: {
        origin_id: 1,
        type: "STATION", // STATION, SECTION, LOOP
    },

    though: [],

    destination: {
        destination_id: 1,
        type: "SECTION", // SECTION, STATION, LOOP
    },
};

const payload2 = {
    origin: {
        origin_id: 1,
        type: "STATION", // STATION, SECTION, LOOP
    },

    though: [
        {
            id: 1,
            type: "SECTION", // STATION, SECTION, LOOP
        },
    ],

    destination: {
        destination_id: 1,
        type: "STATION", // SECTION, STATION, LOOP
    },
};

const payload3 = {
    origin: {
        origin_id: 1,
        type: "STATION", // STATION, SECTION, LOOP
    },

    though: [
        {
            id: 1,
            type: "SECTION", // STATION, SECTION, LOOP
        },
    ],

    destination: {
        destination_id: 1,
        type: "LOOP", // SECTION, STATION, LOOP
    },
};

const payload4 = {
    origin: {
        origin_id: 1,
        type: "STATION", // STATION, SECTION, LOOP
    },

    though: [
        {
            id: 1,
            type: "SECTION", // STATION, SECTION, LOOP
        },
        {
            id: 2,
            type: "STATION", // STATION, SECTION, LOOP
        },
        {
            id: 4,
            type: "SECTION", // STATION, SECTION, LOOP
        },
        {
            id: 4,
            type: "STATION", // STATION, SECTION, LOOP
        },
        {
            id: 1,
            type: "SECTION", // STATION, SECTION, LOOP
        },
        {
            id: 1,
            type: "STATION", // STATION, SECTION, LOOP
        },
        {
            id: 1,
            type: "SECTION", // STATION, SECTION, LOOP
        },
    ],

    destination: {
        destination_id: 1,
        type: "STATION", // SECTION, STATION, LOOP
    },
};

// A=========B=========C      =========D===========E
// const payload = {
//     origin: {
//         origin_id: 1,
//         type: "SECTION",
//     },

//     though: [],

//     destination: {
//         destination_id: 4,
//         type: "STATION",
//     },
// };

// const payload = {
// origin: {
//     origin_id: 1,
//     type: "SECTION",
// },

//     though: [],

// destination: {
//     destination_id: 4,
//     type: "LOOP",
// },
// };

// const payload = {
// origin: {
//     origin_id: 1,
//     type: "SECTION",
// },

//     thought: [
// {
//     id: 1,
//     type: "STATION",
// },
//         {
//             id: 1,
//             type: "SECTION",
//         },
//         {
//             id: 1,
//             type: "STATION",
//         },
//     ],

// destination: {
//     destination_id: 4,
//     type: "SECTION",
// },
// };

// const paylaod = {
//     origin: {
//         origin_id: 1,
//         type: "SECTION",
//     },

//     thought: [
//         {
//             id: 1,
//             type: "LOOP",
//         },
//     ],
//     destination: {
//         destination_id: 4,
//         type: "SECTION",
//     },
// };

const train1 = {
    name: "RBQ",
    origin: "NAI",
    destination: "RIR",
};


const train2 = {
    name: "RBQ",
    origin: "RIR",
    destination: "KIT",
};